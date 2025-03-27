<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\Keluarga;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\FamilyMemberDocument;
use App\Services\CitizenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    public function index()
    {
        if (Auth::guard('web')->check()) {
            $userData = Auth::guard('web')->user();

        } elseif (Auth::guard('penduduk')->check()) {
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

                                Log::info('Family members retrieved successfully', [
                                    'count' => count($userData->family_members),
                                    'kk' => $userData->citizen_data['kk'],
                                ]);
                            } else {
                                Log::error('Failed to retrieve family members', [
                                    'kk' => $userData->citizen_data['kk'],
                                    'response' => $familyData,
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error fetching citizen data: ' . $e->getMessage());
                }
            }
        } else {
            return redirect()
                ->route('login')
                ->with('error', 'Please login to access your profile');
        }

        return view('user.profile.index', compact('userData'));
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

        return redirect()
            ->route('user.profile.index')
            ->with('success', 'Profile created successfully');
    }

    public function edit()
    {
        if (Auth::guard('web')->check()) {
            $userData = Auth::guard('web')->user();
        } elseif (Auth::guard('penduduk')->check()) {
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
            return redirect()
                ->route('login')
                ->with('error', 'Please login to access your profile');
        }

        // Get all provinces for the dropdown if needed
        $provinsi = Provinsi::all();

        return view('user.profile.edit', compact('userData', 'provinsi'));
    }

    public function update(Request $request)
    {
        if (Auth::guard('web')->check()) {
            $userData = Auth::guard('web')->user();
        } elseif (Auth::guard('penduduk')->check()) {
            $userData = Auth::guard('penduduk')->user();
        } else {
            return redirect()
                ->route('login')
                ->with('error', 'Please login to update your profile');
        }

        $request->validate([
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
                return back()->withErrors(['current_password' => 'The provided password does not match our records.']);
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

                $keluarga->save();
            }
        }

        $userData->save();

        return redirect()
            ->route('user.profile.index')
            ->with('success', 'Profile updated successfully');
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

            $tagLokasi = FamilyMemberDocument::where('nik', $nik)
                ->whereNotNull('tag_lokasi')
                ->value('tag_lokasi');

            if (!$tagLokasi) {
                $penduduk = Penduduk::where('nik', $nik)->first();
                $tagLokasi = $penduduk ? $penduduk->tag_lokasi : null;
            }

            return response()->json([
                'success' => true,
                'documents' => [
                    'foto_diri' => $documents['foto_diri'] ?? null,
                    'foto_ktp' => $documents['foto_ktp'] ?? null,
                    'foto_akta' => $documents['foto_akta'] ?? null,
                    'ijazah' => $documents['ijazah'] ?? null,
                    'foto_kk' => $documents['foto_kk'] ?? null, 
                    'foto_rumah' => $documents['foto_rumah'] ?? null,
                ],
                'tag_lokasi' => $tagLokasi,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting family member documents: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error retrieving document information',
                ],
                500,
            );
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

            $fullName = '';
            $user = auth()
                ->guard('penduduk')
                ->user();

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

            $user = auth()->guard('penduduk')->user();

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
                ],
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
            Log::error('Error uploading document: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengunggah dokumen: ' . $e->getMessage(),
                ],
                500,
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

            if (!$document || empty($document->file_path)) {
                abort(404, 'Document not found');
            }

            if (!Storage::disk('public')->exists($document->file_path)) {
                abort(404, 'Document file not found');
            }

            $contentType = $document->mime_type ?: 'application/octet-stream';

            return response()->file(storage_path('app/public/' . $document->file_path), [
                'Content-Type' => $contentType,
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
            if (!in_array($documentType, ['foto_diri', 'foto_ktp', 'foto_akta', 'ijazah', 'foto_kk', 'foto_rumah'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe dokumen tidak valid'
                ], 400);
            }

            $document = FamilyMemberDocument::where('nik', $nik)
                ->where('document_type', $documentType)
                ->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }

            if (!empty($document->file_path) && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
}
