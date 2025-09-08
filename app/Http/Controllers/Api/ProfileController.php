<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\CitizenService;
use App\Models\User;
use App\Models\FamilyMemberDocument;
use App\Models\Keluarga;
use App\Models\Penduduk;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    public function index()
    {
        $userData = null;

        // Check for token authentication first
        if (auth('sanctum')->check()) {
            $userData = auth('sanctum')->user();

            // Get additional data if this is a Penduduk user
            if ($userData instanceof Penduduk && $userData->nik) {
                try {
                    $citizenData = $this->citizenService->getCitizenByNIK((int) $userData->nik);
                    if ($citizenData && isset($citizenData['data'])) {
                        $userData->citizen_data = $citizenData['data'];

                        if (isset($userData->citizen_data['kk'])) {
                            $userData->no_kk = $userData->citizen_data['kk'];
                            $familyData = $this->citizenService->getFamilyMembersByKK($userData->citizen_data['kk']);

                            if ($familyData && isset($familyData['data'])) {
                                $userData->family_members = $familyData['data'];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error fetching citizen data: ' . $e->getMessage());
                }
            }
        }
        // Fallback to session-based auth if no token is present
        else if (Auth::guard('web')->check()) {
            $userData = Auth::guard('web')->user();
        } else if (Auth::guard('penduduk')->check()) {
            $userData = Auth::guard('penduduk')->user();

            if ($userData) {
                try {
                    $citizenData = $this->citizenService->getCitizenByNIK((int) $userData->nik);
                    if ($citizenData && isset($citizenData['data'])) {
                        $userData->citizen_data = $citizenData['data'];

                        if (isset($userData->citizen_data['kk'])) {
                            $userData->no_kk = $userData->citizen_data['kk'];
                            $familyData = $this->citizenService->getFamilyMembersByKK($userData->citizen_data['kk']);

                            if ($familyData && isset($familyData['data'])) {
                                $userData->family_members = $familyData['data'];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error fetching citizen data: ' . $e->getMessage());
                }
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $userData,
        ]);
    }

    public function create()
    {
        return view('user.profile.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile created successfully',
            'data' => $user,
        ], 201);
    }

    public function edit()
    {
        $userData = null;

        // Check for token authentication first
        if (auth('sanctum')->check()) {
            $userData = auth('sanctum')->user();

            // Get additional data if this is a Penduduk user
            if ($userData instanceof Penduduk) {
                try {
                    $citizenData = $this->citizenService->getCitizenByNIK((int) $userData->nik);
                    if ($citizenData && isset($citizenData['data'])) {
                        $userData->citizen_data = $citizenData['data'];
                    }
                } catch (\Exception $e) {
                    Log::error('Error fetching citizen data: ' . $e->getMessage());
                }
            }
        }
        // Fallback to session-based auth if no token is present
        else if (Auth::guard('web')->check()) {
            $userData = Auth::guard('web')->user();
        } else if (Auth::guard('penduduk')->check()) {
            $userData = Auth::guard('penduduk')->user();

            if ($userData) {
                try {
                    $citizenData = $this->citizenService->getCitizenByNIK((int) $userData->nik);
                    if ($citizenData && isset($citizenData['data'])) {
                        $userData->citizen_data = $citizenData['data'];
                    }
                } catch (\Exception $e) {
                    Log::error('Error fetching citizen data: ' . $e->getMessage());
                }
            }
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $userData,
        ]);
    }

    public function update(Request $request)
    {
        $userData = null;

        // Check for token authentication first
        if (auth('sanctum')->check()) {
            $userData = auth('sanctum')->user();
        }
        // Fallback to session-based auth if no token is present
        else if (Auth::guard('web')->check()) {
            $userData = Auth::guard('web')->user();
        } else if (Auth::guard('penduduk')->check()) {
            $userData = Auth::guard('penduduk')->user();
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 401);
        }

        try {
            $validated = $request->validate([
                'no_hp' => 'required|string|max:15',
                'alamat' => 'nullable|string|max:255',
                'rt' => 'nullable|string|max:3',
                'rw' => 'nullable|string|max:3',
                'tag_lokasi' => 'nullable|string|max:255',
                'current_password' => 'nullable|required_with:new_password',
                'new_password' => 'nullable|min:6|confirmed',
                'foto_diri' => 'nullable|image|max:4096',
                'foto_ktp' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
                'foto_akta' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
                'ijazah' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
                'foto_rumah' => 'nullable|image|max:4096',
                'foto_kk' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
            ]);

            $userData->no_hp = $request->no_hp;

            if ($request->filled('alamat')) {
                $userData->alamat = $request->alamat;
            }
            if ($request->filled('rt')) {
                $userData->rt = $request->rt;
            }
            if ($request->filled('rw')) {
                $userData->rw = $request->rw;
            }
            if ($request->filled('tag_lokasi')) {
                $userData->tag_lokasi = $request->tag_lokasi;
            }

            if ($request->filled('new_password')) {
                if (Hash::check($request->current_password, $userData->password)) {
                    $userData->password = Hash::make($request->new_password);
                } else {
                    return response()->json([
                        'success' => false,
                        'errors' => ['current_password' => ['The provided password does not match our records.']]
                    ], 422);
                }
            }

            $this->handleFileUpload($request, $userData, 'foto_diri');
            $this->handleFileUpload($request, $userData, 'foto_ktp');
            $this->handleFileUpload($request, $userData, 'foto_akta');
            $this->handleFileUpload($request, $userData, 'ijazah');

            if ($request->hasFile('foto_rumah') || $request->hasFile('foto_kk')) {
                if ($userData->no_kk) {
                    $keluarga = Keluarga::firstOrCreate(['no_kk' => $userData->no_kk]);

                    $this->handleFileUpload($request, $keluarga, 'foto_rumah');
                    $this->handleFileUpload($request, $keluarga, 'foto_kk');
                }
            }

            $userData->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $userData
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function handleFileUpload(Request $request, $model, $field)
    {
        if ($request->hasFile($field)) {
            if (!empty($model->$field)) {
                Storage::disk('public')->delete($model->$field);
            }

            $file = $request->file($field);
            $filename = time() . '_' . $field . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/' . $field, $filename, 'public');
            $model->$field = $path;
        }
    }

    public function updateLocation(Request $request)
    {
        try {
            $request->validate([
                'tag_lokasi' => 'required|string',
                'alamat' => 'required|string|max:500'
            ]);

            // Auth: token first, then penduduk guard
            if (auth('sanctum')->check()) {
                $userData = auth('sanctum')->user();
            } else if (Auth::guard('penduduk')->check()) {
                $userData = Auth::guard('penduduk')->user();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Ambil KK dari API berdasarkan NIK
            $kk = null;
            try {
                if (!empty($userData->nik)) {
                    $citizenData = $this->citizenService->getCitizenByNIK((int) $userData->nik);
                    if ($citizenData && isset($citizenData['data'])) {
                        $kk = $citizenData['data']['kk'] ?? ($citizenData['data']['no_kk'] ?? null);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch citizen data for KK', [
                    'nik' => $userData->nik ?? null,
                    'error' => $e->getMessage()
                ]);
            }

            if (!$kk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor KK tidak ditemukan untuk pengguna ini'
                ], 400);
            }

            Log::info('API: updating location for family by KK', [
                'kk' => $kk,
                'coordinate' => $request->tag_lokasi,
                'address' => $request->alamat,
                'updater_nik' => $userData->nik ?? null
            ]);

            // Ambil anggota keluarga dari API
            $familyMembers = [];
            $apiFamily = $this->citizenService->getFamilyMembersByKK($kk);
            if ($apiFamily && isset($apiFamily['data']) && is_array($apiFamily['data'])) {
                $familyMembers = $apiFamily['data'];
            }

            $updatedMembers = [];
            $failedUpdates = [];

            foreach ($familyMembers as $member) {
                if (empty($member['nik'])) {
                    continue;
                }
                $memberNik = (int) $member['nik'];

                // Update lokal DB (buat jika belum ada)
                try {
                    $penduduk = Penduduk::where('nik', $memberNik)->first();
                    if ($penduduk) {
                        $penduduk->tag_lokasi = $request->tag_lokasi;
                        $penduduk->alamat = $request->alamat;
                        $penduduk->save();
                    } else {
                        Penduduk::create([
                            'nik' => $memberNik,
                            'password' => Hash::make(Str::random(10)),
                            'tag_lokasi' => $request->tag_lokasi,
                            'alamat' => $request->alamat,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('API: failed updating local DB for member', [
                        'nik' => $memberNik,
                        'error' => $e->getMessage()
                    ]);
                }

                // Update ke API eksternal
                try {
                    $resp = $this->citizenService->updateCitizen($memberNik, [
                        'coordinate' => $request->tag_lokasi,
                        'address' => $request->alamat
                    ]);

                    if (isset($resp['status']) && $resp['status'] === 'ERROR') {
                        $failedUpdates[] = [
                            'nik' => $memberNik,
                            'name' => $member['full_name'] ?? $member['nama'] ?? 'Unknown',
                            'error' => $resp['message'] ?? 'Unknown error'
                        ];
                        Log::warning('API: external update failed for member', [
                            'nik' => $memberNik,
                            'error' => $resp['message'] ?? 'Unknown error'
                        ]);
                    } else {
                        $updatedMembers[] = [
                            'nik' => $memberNik,
                            'name' => $member['full_name'] ?? $member['nama'] ?? 'Unknown'
                        ];
                    }
                } catch (\Exception $e) {
                    $failedUpdates[] = [
                        'nik' => $memberNik,
                        'name' => $member['full_name'] ?? $member['nama'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                    Log::error('API: exception updating external API for member', [
                        'nik' => $memberNik,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Lokasi dan alamat keluarga berhasil diperbarui',
                'data' => [
                    'updated_members' => $updatedMembers,
                    'failed_updates' => $failedUpdates,
                    'total_members' => count($familyMembers)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in API updateLocation (family): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui lokasi keluarga',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFamilyMemberDocuments($nik)
    {
        try {
            $documents = FamilyMemberDocument::where('nik', $nik)
                ->get()
                ->groupBy('document_type')
                ->map(function ($docs) {
                    $doc = $docs->first();

                    return [
                        'exists' => true,
                        'file_path' => $doc->file_path,
                        'extension' => $doc->extension,
                        'updated_at' => $doc->updated_at->format('Y-m-d H:i:s'),
                        'preview_url' => in_array(strtolower($doc->extension), ['jpg', 'jpeg', 'png']) ? asset('storage/' . $doc->file_path) : null,
                    ];
                })
                ->toArray();

            return response()->json([
                'success' => true,
                'documents' => [
                    'foto_diri' => $documents['foto_diri'] ?? null,
                    'foto_ktp' => $documents['foto_ktp'] ?? null,
                    'foto_akta' => $documents['foto_akta'] ?? null,
                    'ijazah' => $documents['ijazah'] ?? null,
                    'foto_kk' => $documents['foto_kk'] ?? null,
                    'foto_rumah' => $documents['foto_rumah'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting family member documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving document information',
            ], 500);
        }
    }

    public function uploadFamilyMemberDocument(Request $request, $nik)
    {
        try {
            $request->validate([
                'file' => 'required|file|max:4096',
                'document_type' => 'required|string|in:foto_diri,foto_ktp,foto_akta,ijazah,foto_kk,foto_rumah',
                'tag_lokasi' => 'nullable|string|max:255',
            ]);

            if ($request->document_type == 'foto_diri') {
                $request->validate(['file' => 'mimes:jpg,jpeg,png']);
            } else {
                $request->validate(['file' => 'mimes:jpg,jpeg,png,pdf']);
            }

            // Check authentication - support both token and session auth
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
            } elseif (auth()->guard('penduduk')->check()) {
                $user = auth()->guard('penduduk')->user();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Please login first',
                ], 401);
            }

            // Now we're sure $user is not null before accessing its properties
            $fullName = '';

            if ($user->nik == $nik) {
                $fullName = $user->nama_lengkap ?? ($user->nama ?? '');
            } else {
                $familyMembers = $user->family_members ?? [];
                foreach ($familyMembers as $member) {
                    if (isset($member['nik']) && $member['nik'] == $nik) {
                        $fullName = $member['full_name'] ?? '';
                        break;
                    }
                }

                if (empty($fullName)) {
                    $penduduk = Penduduk::where('nik', $nik)->first();
                    if ($penduduk) {
                        $fullName = $penduduk->nama_lengkap ?? ($penduduk->nama ?? '');
                    }
                }

                if (empty($fullName)) {
                    try {
                        $citizen = $this->citizenService->getCitizenByNIK((int) $nik);
                        if ($citizen && isset($citizen['data']) && isset($citizen['data']['full_name'])) {
                            $fullName = $citizen['data']['full_name'];
                        } elseif ($citizen && isset($citizen['data']) && isset($citizen['data']['nama'])) {
                            $fullName = $citizen['data']['nama'];
                        }
                    } catch (\Exception $e) {
                        Log::error('Error fetching citizen data: ' . $e->getMessage());
                    }
                }
            }

            if (empty($fullName)) {
                $fullName = "Penduduk NIK {$nik}";
            }

            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::slug(Str::limit($fullName, 20)) . '.' . $extension;
            $path = $file->storeAs('uploads/documents/' . $request->document_type, $fileName, 'public');

            // Create penduduk record if it doesn't exist and user is uploading their own document
            if ($user->nik == $nik) {
                $penduduk = Penduduk::firstOrNew(['nik' => $nik]);
                if (!$penduduk->exists) {
                    $penduduk->password = Hash::make(Str::random(10));
                    $penduduk->save();
                }
            }

            $tagLokasi = $request->tag_lokasi;

            if (!empty($tagLokasi)) {
                try {
                    $data = [
                        'coordinate' => $tagLokasi
                    ];

                    Log::info('Updating citizen coordinates via document upload', [
                        'nik' => $nik,
                        'coordinate' => $tagLokasi
                    ]);

                    $response = $this->citizenService->updateCitizen((int) $nik, $data);

                    Log::info('API coordinate update response', [
                        'nik' => $nik,
                        'response' => $response
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error updating coordinates in API: ' . $e->getMessage(), [
                        'nik' => $nik,
                        'coordinate' => $tagLokasi
                    ]);
                }
            }

            $document = FamilyMemberDocument::updateOrCreate(
                [
                    'nik' => $nik,
                    'document_type' => $request->document_type,
                ],
                [
                    'file_path' => $path,
                    'file_name' => $fileName,
                    'mime_type' => $file->getMimeType(),
                    'extension' => $extension,
                    'file_size' => $file->getSize() / 1024,
                    'tag_lokasi' => $tagLokasi,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil diunggah',
                'document' => [
                    'id' => $document->id,
                    'file_name' => $document->file_name,
                    'file_path' => $document->file_path,
                    'preview_url' => in_array(strtolower($extension), ['jpg', 'jpeg', 'png']) ? asset('storage/' . $path) : null,
                    'updated_at' => $document->updated_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading document: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengunggah dokumen: ' . $e->getMessage(),
                ],
                500
            );
        }
    }
    public function viewFamilyMemberDocument($nik, $documentType)
    {
        try {
            if (!in_array($documentType, ['foto_diri', 'foto_ktp', 'foto_akta', 'ijazah', 'foto_kk', 'foto_rumah'])) {
                abort(400, 'Invalid document type');
            }

            $document = FamilyMemberDocument::where('nik', $nik)
                ->where('document_type', $documentType)
                ->first();

            if (!$document || empty($document->file_path) || !Storage::disk('public')->exists($document->file_path)) {
                abort(404, 'Document not found');
            }

            return response()->file(storage_path('app/public/' . $document->file_path), [
                'Content-Type' => $document->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Error viewing document: ' . $e->getMessage());
            abort(500, 'Error viewing document');
        }
    }


    public function deleteFamilyMemberDocument($nik, $documentType)
    {
        try {
            // Check for token authentication first
            if (auth('sanctum')->check()) {
                $user = auth('sanctum')->user();
            }
            // Fallback to session-based auth if no token is present
            else if (Auth::guard('penduduk')->check()) {
                $user = Auth::guard('penduduk')->user();
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $document = FamilyMemberDocument::where('nik', $nik)
                ->where('document_type', $documentType)
                ->latest()
                ->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting document',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
