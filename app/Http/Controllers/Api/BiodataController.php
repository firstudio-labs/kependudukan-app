<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use App\Models\InformasiUsahaChangeRequest;
use App\Models\InformasiUsaha;
use App\Models\User;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BiodataController extends Controller
{
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    /**
     * Get current user biodata
     */
    public function getBiodata(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            // Get citizen data from API - sama dengan ProfileController
            $citizen = $this->citizenService->getCitizenByNIK((int) $user->nik);
            
            if (!$citizen) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Data penduduk tidak ditemukan'
                ], 404);
            }

            // Extract data - sama dengan ProfileController
            $citizenData = $citizen['data'] ?? $citizen ?? [];
            $villageId = $citizenData['village_id'] ?? $citizen['village_id'] ?? $citizenData['villages_id'] ?? $citizen['villages_id'] ?? null;

            // Get family members if KK exists
            $familyMembers = [];
            if (isset($citizenData['kk'])) {
                try {
                    $familyData = $this->citizenService->getFamilyMembersByKK($citizenData['kk']);
                    if ($familyData && isset($familyData['data'])) {
                        $familyMembers = $familyData['data'];
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to retrieve family members: ' . $e->getMessage());
                }
            }

            // Get jobs for dropdown (optional list)
            $jobs = [];
            try {
                $jobs = app(\App\Services\JobService::class)->getAllJobs();
            } catch (\Exception $e) {
                Log::warning('Failed to retrieve jobs list: ' . $e->getMessage());
            }

            // Informasi Usaha: cari milik user atau satu KK
            $informasiUsaha = null;
            try {
                $informasiUsaha = \App\Models\InformasiUsaha::where('penduduk_id', $user->id)->first();
                if (!$informasiUsaha && !empty($citizenData['kk'])) {
                    $informasiUsaha = \App\Models\InformasiUsaha::where('kk', $citizenData['kk'])->first();
                }
            } catch (\Exception $e) {
                $informasiUsaha = null;
            }

            $informasiUsahaPayload = null;
            if ($informasiUsaha) {
                $lat = null; $lng = null;
                if (!empty($informasiUsaha->tag_lokasi) && strpos($informasiUsaha->tag_lokasi, ',') !== false) {
                    [$latStr, $lngStr] = array_map('trim', explode(',', $informasiUsaha->tag_lokasi));
                    $lat = $latStr !== '' ? (float) $latStr : null;
                    $lng = $lngStr !== '' ? (float) $lngStr : null;
                }

                $fotoUrl = $informasiUsaha->foto_url ?? $informasiUsaha->foto ?? null;
                if ($fotoUrl && !preg_match('#^https?://#', $fotoUrl)) {
                    $fotoUrl = asset('storage/' . ltrim($fotoUrl, '/'));
                }

                $informasiUsahaPayload = [
                    'id' => $informasiUsaha->id,
                    'nama_usaha' => $informasiUsaha->nama_usaha,
                    'kelompok_usaha' => $informasiUsaha->kelompok_usaha,
                    'alamat' => $informasiUsaha->alamat,
                    'tag_lokasi' => $informasiUsaha->tag_lokasi,
                    'tag_lat' => $lat,
                    'tag_lng' => $lng,
                    'foto_url' => $fotoUrl,
                    'kk' => $informasiUsaha->kk ?? null,
                    'is_owner' => $informasiUsaha->penduduk_id ? ((string)$informasiUsaha->penduduk_id === (string)$user->id) : false,
                ];
            }

            return response()->json([
                'status' => 'SUCCESS',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'nik' => $user->nik,
                        'email' => $user->email,
                        'no_hp' => $user->no_hp ?? null,
                    ],
                    'biodata' => $citizenData,
                    'informasi_usaha' => $informasiUsahaPayload,
                    'family_members' => $familyMembers,
                    'jobs' => $jobs,
                    'village_id' => $villageId
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting biodata: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat mengambil data biodata'
            ], 500);
        }
    }

    /**
     * Request biodata update approval - MAIN FUNCTION FOR MOBILE APP
     */
    public function requestUpdate(Request $request)
{
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'User tidak ditemukan'
            ], 401);
        }

        // Validasi rules - tambahkan nik
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string', // Tambahkan validasi NIK
            'full_name' => 'required|string|max:255',
            'kk' => 'nullable|string',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'age' => 'nullable|numeric',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'province_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'sub_district_id' => 'required|numeric',
            'village_id' => 'required|numeric',
            'blood_type' => 'nullable|numeric',
            'education_status' => 'nullable|numeric',
            'job_type_id' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // PERBAIKAN: Gunakan NIK yang dikirim dari mobile app
        $requestedNik = $request->input('nik');
        
        // Get current citizen data berdasarkan NIK yang dikirim
        $citizen = $this->citizenService->getCitizenByNIK((int) $requestedNik);
        if (!$citizen) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Data penduduk tidak ditemukan'
            ], 404);
        }

        // Extract village_id dan current_data berdasarkan NIK yang dikirim
        $villageId = $citizen['data']['village_id'] ?? $citizen['village_id'] ?? $citizen['data']['villages_id'] ?? $citizen['villages_id'] ?? null;
        $currentData = $citizen['data'] ?? $citizen ?? [];

        if (!$villageId) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Data desa tidak ditemukan'
            ], 400);
        }

        // Check if there's already a pending request untuk NIK yang dikirim
        $existingRequest = ProfileChangeRequest::where('nik', $requestedNik)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Anda masih memiliki permintaan perubahan yang sedang diproses'
            ], 400);
        }

        // Get validated data
        $validated = $validator->validated();

        // Create profile change request dengan NIK yang dikirim
        $profileChangeRequest = ProfileChangeRequest::create([
            'nik' => $requestedNik, // PERBAIKAN: Gunakan NIK yang dikirim
            'village_id' => $villageId,
            'current_data' => $currentData, // Data asli dari NIK yang dikirim
            'requested_changes' => $validated,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // OPTIONAL: Tangani perubahan Informasi Usaha bila dikirim dari mobile
        $usahaInput = $request->input('informasi_usaha', []);
        $hasUsahaInput = is_array($usahaInput) && (count(array_filter($usahaInput, fn($v)=>$v!==null && $v!=='')) > 0);

        if ($hasUsahaInput) {
            // Ambil KK dan cari current usaha (berdasarkan KK prioritas)
            $kkNumber = $validated['kk'] ?? ($currentData['kk'] ?? null);
            $existingUsaha = null;
            if ($kkNumber) {
                $existingUsaha = InformasiUsaha::where('kk', $kkNumber)->first();
            }

            // Siapkan current_data untuk usaha
            $usahaCurrent = $existingUsaha ? $existingUsaha->only([
                'nama_usaha','kelompok_usaha','alamat','tag_lokasi','province_id','districts_id','sub_districts_id','villages_id','foto','kk'
            ]) : [];

            // Ambil foto jika diupload
            $fotoPath = null;
            if ($request->hasFile('informasi_usaha_foto')) {
                $fotoPath = $request->file('informasi_usaha_foto')->store('informasi_usaha_tmp', 'public');
            } elseif (!empty($usahaInput['foto'])) {
                // Jika dikirim base64 (opsional), abaikan untuk simpel, atau simpan apa adanya sebagai placeholder
                $fotoPath = $usahaInput['foto'];
            }

            $usahaRequested = [
                'nama_usaha' => $usahaInput['nama_usaha'] ?? null,
                'kelompok_usaha' => $usahaInput['kelompok_usaha'] ?? null,
                'alamat' => $usahaInput['alamat'] ?? null,
                'tag_lokasi' => $usahaInput['tag_lokasi'] ?? null,
                'province_id' => $usahaInput['province_id'] ?? ($validated['province_id'] ?? null),
                'districts_id' => $usahaInput['districts_id'] ?? ($validated['district_id'] ?? null),
                'sub_districts_id' => $usahaInput['sub_districts_id'] ?? ($validated['sub_district_id'] ?? null),
                'villages_id' => $usahaInput['villages_id'] ?? ($validated['village_id'] ?? null),
                'foto' => $fotoPath,
                'kk' => $kkNumber,
            ];

            InformasiUsahaChangeRequest::create([
                'penduduk_id' => $currentData['id'] ?? null,
                'informasi_usaha_id' => $existingUsaha->id ?? null,
                'requested_changes' => $usahaRequested,
                'current_data' => $usahaCurrent,
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Permintaan perubahan biodata berhasil dikirim ke admin desa',
            'data' => [
                'request_id' => $profileChangeRequest->id,
                'status' => 'pending',
                'requested_at' => $profileChangeRequest->requested_at,
                'requested_changes' => $validated,
                'village_id' => $villageId,
                'message' => 'Permintaan Anda sedang menunggu approval dari admin desa'
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error requesting biodata update: ' . $e->getMessage());
        return response()->json([
            'status' => 'ERROR',
            'message' => 'Terjadi kesalahan saat mengirim permintaan perubahan'
        ], 500);
    }
}
    /**
     * Get user's biodata change history
     */
    public function getHistory(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            // Dukung opsi mengambil history untuk anggota keluarga via query ?nik=...
            $targetNik = $request->query('nik', $user->nik);

            $history = ProfileChangeRequest::where('nik', $targetNik)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'status' => $request->status,
                        'requested_at' => $request->requested_at,
                        'reviewed_at' => $request->reviewed_at,
                        'reviewer_name' => $request->reviewer ? $request->reviewer->name : null,
                        'reviewer_note' => $request->reviewer_note,
                        'requested_changes' => $request->requested_changes,
                        'current_data' => $request->current_data,
                        'status_message' => $this->getStatusMessage($request->status),
                    ];
                });

            return response()->json([
                'status' => 'SUCCESS',
                'data' => [
                    'history' => $history,
                    'total_requests' => $history->count(),
                    'pending_requests' => $history->where('status', 'pending')->count(),
                    'approved_requests' => $history->where('status', 'approved')->count(),
                    'rejected_requests' => $history->where('status', 'rejected')->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting biodata history: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat mengambil history'
            ], 500);
        }
    }

    /**
     * Get specific request detail
     */
    public function getRequestDetail(Request $request, $requestId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            $profileRequest = ProfileChangeRequest::where('id', $requestId)
                ->where('nik', $user->nik)
                ->first();

            if (!$profileRequest) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Permintaan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'SUCCESS',
                'data' => [
                    'id' => $profileRequest->id,
                    'status' => $profileRequest->status,
                    'status_message' => $this->getStatusMessage($profileRequest->status),
                    'requested_at' => $profileRequest->requested_at,
                    'reviewed_at' => $profileRequest->reviewed_at,
                    'reviewer_name' => $profileRequest->reviewer ? $profileRequest->reviewer->name : null,
                    'reviewer_note' => $profileRequest->reviewer_note,
                    'requested_changes' => $profileRequest->requested_changes,
                    'current_data' => $profileRequest->current_data,
                    'village_id' => $profileRequest->village_id,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting request detail: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat mengambil detail permintaan'
            ], 500);
        }
    }

    /**
     * Cancel pending request
     */
    public function cancelRequest(Request $request, $requestId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            $profileRequest = ProfileChangeRequest::where('id', $requestId)
                ->where('nik', $user->nik)
                ->where('status', 'pending')
                ->first();

            if (!$profileRequest) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'Permintaan tidak ditemukan atau tidak dapat dibatalkan'
                ], 404);
            }

            $profileRequest->delete();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Permintaan perubahan biodata berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error canceling request: ' . $e->getMessage());
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Terjadi kesalahan saat membatalkan permintaan'
            ], 500);
        }
    }

    /**
     * Get status message for mobile display
     */
    private function getStatusMessage($status)
    {
        switch ($status) {
            case 'pending':
                return 'Menunggu approval dari admin desa';
            case 'approved':
                return 'Permintaan disetujui dan data telah diperbarui';
            case 'rejected':
                return 'Permintaan ditolak oleh admin desa';
            default:
                return 'Status tidak diketahui';
        }
    }
}
