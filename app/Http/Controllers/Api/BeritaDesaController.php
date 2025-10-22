<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use Illuminate\Http\Request;
use App\Services\CitizenService;
use App\Services\WilayahService;

class BeritaDesaController extends Controller
{
    protected $wilayahService;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
    }

    public function index(Request $request, CitizenService $citizenService)
    {
        try {
            // Get token owner from request attributes
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Hanya izinkan penduduk (bukan admin user)
            if ($request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya penduduk yang dapat mengakses daftar berita desa ini'
                ], 403);
            }

            // Ambil NIK penduduk dan cari village_id
            $nik = $tokenOwner->nik ?? null;
            $citizenData = $nik ? $citizenService->getCitizenByNIK($nik) : null;

            $villageId = null;
            if (is_array($citizenData)) {
                // Beberapa kemungkinan struktur response
                $payload = $citizenData['data'] ?? $citizenData;
                if (isset($payload['villages_id'])) {
                    $villageId = $payload['villages_id'];
                } elseif (isset($payload['village_id'])) {
                    $villageId = $payload['village_id'];
                } elseif (isset($payload['village']['id'])) {
                    $villageId = $payload['village']['id'];
                }
            }

            if (!$villageId) {
                return response()->json([
                    'status' => 'success',
                    'data' => [],
                    'message' => 'Data desa tidak ditemukan untuk akun ini'
                ], 200);
            }

            $query = BeritaDesa::query();
            $query->where('villages_id', $villageId)
                  ->where('status', 'published');

            // Handle search parameter
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            // Handle pagination
            $perPage = $request->input('per_page', 10);
            $berita = $query->latest()->paginate($perPage);

            // Transform data to include gambar_url and wilayah_info
            $items = collect($berita->items())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'deskripsi' => $item->deskripsi,
                    'komentar' => $item->komentar,
                    'gambar' => $item->gambar,
                    'gambar_url' => $item->gambar_url, // URL lengkap gambar
                    'user_id' => $item->user_id,
                    'wilayah_info' => $this->getWilayahInfo($item), // Added wilayah info
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $items,
                'meta' => [
                    'current_page' => $berita->currentPage(),
                    'per_page' => $berita->perPage(),
                    'total' => $berita->total(),
                    'last_page' => $berita->lastPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch berita desa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, CitizenService $citizenService)
    {
        try {
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner || $request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'komentar' => 'nullable|string',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
                'submit_for_approval' => 'nullable|boolean'
            ]);

            $nik = $tokenOwner->nik ?? null;
            $citizenData = $nik ? $citizenService->getCitizenByNIK($nik) : null;
            $payload = is_array($citizenData) ? ($citizenData['data'] ?? $citizenData) : [];

            $provinceId = $payload['province_id'] ?? $payload['provinsi_id'] ?? null;
            $districtId = $payload['district_id'] ?? $payload['districts_id'] ?? null;
            $subDistrictId = $payload['sub_district_id'] ?? $payload['sub_districts_id'] ?? null;
            $villageId = $payload['villages_id'] ?? $payload['village_id'] ?? null;

            if (!$villageId) {
                return response()->json(['status' => 'error', 'message' => 'Data desa tidak ditemukan untuk akun ini'], 422);
            }

            // Cari user_id dari tabel users yang memiliki villages_id yang sama dengan penduduk
            $user = \App\Models\User::where('villages_id', $villageId)->first();
            
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'Tidak ada user admin desa untuk desa ini'], 422);
            }

            $data = [
                'judul' => $request->input('judul'),
                'deskripsi' => $request->input('deskripsi'),
                'komentar' => $request->input('komentar'),
                'user_id' => $user->id, // Gunakan user_id dari admin desa yang memiliki villages_id yang sama
                'nik_penduduk' => $nik, // NIK penduduk yang membuat berita
                'province_id' => $provinceId ? (int) $provinceId : null,
                'districts_id' => $districtId ? (int) $districtId : null,
                'sub_districts_id' => $subDistrictId ? (int) $subDistrictId : null,
                'villages_id' => (int) $villageId,
                // Semua berita dari penduduk disimpan sebagai diarsipkan; admin akan mem-publish dari panel
                'status' => 'archived',
            ];

            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $titleSlug = \Illuminate\Support\Str::slug(substr($request->input('judul'), 0, 30));
                $filename = time() . '_' . $titleSlug . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/documents/berita-desa', $filename, 'public');
                $data['gambar'] = $path;
            }

            $berita = BeritaDesa::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Berita diarsipkan dan menunggu verifikasi admin desa',
                'data' => $berita
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('BeritaDesa store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'nik' => $nik ?? null,
                'village_id' => $villageId ?? null
            ]);
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan berita: ' . $e->getMessage()], 500);
        }
    }

    public function sendApproval(Request $request, $id)
    {
        try {
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner || $request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }

            $berita = BeritaDesa::where('id', $id)
                ->where(function ($q) use ($tokenOwner) {
                    $q->where('user_id', $tokenOwner->id)->orWhereNull('user_id');
                })
                ->firstOrFail();

            if ($berita->status === 'published') {
                return response()->json(['status' => 'success', 'message' => 'Berita sudah dipublikasikan'], 200);
            }

            // Tidak ada status pending lagi; biarkan tetap archived dan informasikan menunggu admin
            return response()->json([
                'status' => 'success',
                'message' => 'Berita menunggu verifikasi admin desa',
                'data' => $berita
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Berita tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim approval: ' . $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id, CitizenService $citizenService)
    {
        try {
            // Get token owner from request attributes
            $tokenOwner = $request->attributes->get('token_owner');
            if (!$tokenOwner) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 401);
            }

            // Hanya izinkan penduduk (bukan admin user)
            if ($request->attributes->get('token_owner_type') !== 'penduduk') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hanya penduduk yang dapat mengakses detail berita desa ini'
                ], 403);
            }

            // Validasi berita milik desa yang sama dengan penduduk
            $berita = BeritaDesa::findOrFail($id);

            $nik = $tokenOwner->nik ?? null;
            $citizenData = $nik ? $citizenService->getCitizenByNIK($nik) : null;
            $villageId = null;
            if (is_array($citizenData)) {
                $payload = $citizenData['data'] ?? $citizenData;
                if (isset($payload['villages_id'])) {
                    $villageId = $payload['villages_id'];
                } elseif (isset($payload['village_id'])) {
                    $villageId = $payload['village_id'];
                } elseif (isset($payload['village']['id'])) {
                    $villageId = $payload['village']['id'];
                }
            }

            if ($villageId && (string)$berita->villages_id !== (string)$villageId) { // Updated field name
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak berhak mengakses berita desa dari desa lain'
                ], 403);
            }

            // Format response with gambar_url and wilayah_info
            $data = [
                'id' => $berita->id,
                'judul' => $berita->judul,
                'deskripsi' => $berita->deskripsi,
                'komentar' => $berita->komentar,
                'gambar' => $berita->gambar,
                'gambar_url' => $berita->gambar_url, // URL lengkap gambar
                'user_id' => $berita->user_id,
                'villages_id' => $berita->villages_id, // Updated field name
                'wilayah_info' => $this->getWilayahInfo($berita), // Added wilayah info
                'created_at' => $berita->created_at,
                'updated_at' => $berita->updated_at,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Berita desa tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch berita desa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wilayah information for a berita
     */
    private function getWilayahInfo($berita)
    {
        $wilayah = [];
        
        // Always set fallback first for safety
        if ($berita->province_id) {
            $wilayah['provinsi'] = 'Provinsi ID: ' . $berita->province_id;
            
            try {
                $provinces = $this->wilayahService->getProvinces();
                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                $province = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                if ($province && isset($province['name'])) {
                    $wilayah['provinsi'] = $province['name'];
                }
            } catch (\Exception $e) {
                // Fallback already set, no need to change
            }
        }
        
        if ($berita->districts_id) {
            $wilayah['kabupaten'] = 'Kabupaten ID: ' . $berita->districts_id;
            
            try {
                if ($berita->province_id) {
                    // Cari province dulu untuk mendapatkan code yang benar
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                
                                if ($kabupatenData && isset($kabupatenData['name'])) {
                                    $wilayah['kabupaten'] = $kabupatenData['name'];
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fallback already set, no need to change
            }
        }
        
        if ($berita->sub_districts_id) {
            $wilayah['kecamatan'] = 'Kecamatan ID: ' . $berita->sub_districts_id;
            
            try {
                if ($berita->districts_id && $berita->province_id) {
                    // Cari province dulu untuk mendapatkan code yang benar
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $berita->sub_districts_id);
                                        
                                        if ($kecamatanData && isset($kecamatanData['name'])) {
                                            $wilayah['kecamatan'] = $kecamatanData['name'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fallback already set, no need to change
            }
        }
        
        if ($berita->villages_id) {
            $wilayah['desa'] = 'Desa ID: ' . $berita->villages_id;
            
            try {
                if ($berita->sub_districts_id && $berita->districts_id && $berita->province_id) {
                    // Cari province dulu untuk mendapatkan code yang benar
                    $provinces = $this->wilayahService->getProvinces();
                    if (is_array($provinces) && !empty($provinces)) {
                        $provinceData = collect($provinces)->firstWhere('id', (int) $berita->province_id);
                        
                        if ($provinceData && isset($provinceData['code'])) {
                            $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                            if (is_array($kabupaten) && !empty($kabupaten)) {
                                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $berita->districts_id);
                                
                                if ($kabupatenData && isset($kabupatenData['code'])) {
                                    $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                                    if (is_array($kecamatan) && !empty($kecamatan)) {
                                        // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                        $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $berita->sub_districts_id);
                                        
                                        if ($kecamatanData && isset($kecamatanData['code'])) {
                                            $desa = $this->wilayahService->getDesa($kecamatanData['code']);
                                            if (is_array($desa) && !empty($desa)) {
                                                // Perbaikan: Gunakan 'id' field, bukan 'code' field
                                                $desaData = collect($desa)->firstWhere('id', (int) $berita->villages_id);
                                                
                                                if ($desaData && isset($desaData['name'])) {
                                                    $wilayah['desa'] = $desaData['name'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Fallback already set, no need to change
            }
        }
        
        return $wilayah;
    }


}

