<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use Illuminate\Http\Request;
use App\Services\CitizenService;
use App\Services\WilayahService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Jobs\PreloadWilayahInfoJob;

class BeritaDesaController extends Controller
{
    protected $wilayahService;
    protected $cacheStore;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
        $this->cacheStore = $this->getCacheStore();
    }

    /**
     * Get cache store - prefer Redis jika tersedia, fallback ke default
     */
    private function getCacheStore()
    {
        try {
            // Coba gunakan Redis jika tersedia
            if (config('cache.default') === 'redis' || config('cache.stores.redis')) {
                return Cache::store('redis');
            }
        } catch (\Exception $e) {
            Log::warning('Redis tidak tersedia, menggunakan default cache: ' . $e->getMessage());
        }
        
        // Fallback ke default cache store
        return Cache::store(config('cache.default', 'file'));
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

            // Build cache key berdasarkan village_id, search, dan pagination
            $search = $request->input('search', '');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $cacheKey = "berita_desa_index_{$villageId}_{$search}_{$perPage}_{$page}";
            $useCache = !$request->has('refresh'); // Support ?refresh=1 untuk bypass cache

            // Gunakan cache store yang optimal (Redis jika tersedia)
            if ($useCache && $this->cacheStore->has($cacheKey)) {
                return response()->json($this->cacheStore->get($cacheKey), 200);
            }

            $query = BeritaDesa::query();
            $query->where('villages_id', $villageId)
                  ->where('status', 'published');

            // Handle search parameter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            // Handle pagination - optimasi dengan select spesifik
            $berita = $query->select([
                'id', 'judul', 'deskripsi', 'komentar', 'gambar',
                'user_id', 'villages_id', 'province_id', 'districts_id', 
                'sub_districts_id', 'created_at', 'updated_at'
            ])->latest()->paginate($perPage);

            // Cek apakah user ingin include wilayah_info (default: true untuk backward compatibility)
            $includeWilayah = $request->input('include_wilayah', '1') !== '0';
            
            // Pre-load semua wilayah info yang dibutuhkan untuk menghindari API call berulang
            // Gunakan queue untuk pre-load jika tidak urgent
            if ($includeWilayah) {
                // Jika cache miss, dispatch job untuk pre-load di background
                if (!$this->cacheStore->has($cacheKey)) {
                    // Pre-load wilayah info secara sync untuk response pertama
                    $wilayahInfoCache = $this->preloadWilayahInfo($berita->items());
                    
                    // Dispatch job untuk pre-load cache untuk halaman berikutnya
                    $itemsForQueue = collect($berita->items())->map(function ($item) {
                        return [
                            'province_id' => $item->province_id,
                            'districts_id' => $item->districts_id,
                            'sub_districts_id' => $item->sub_districts_id,
                            'villages_id' => $item->villages_id,
                        ];
                    })->toArray();
                    
                    PreloadWilayahInfoJob::dispatch($itemsForQueue)->onQueue('default');
                } else {
                    // Jika cache hit, load dari cache
                    $wilayahInfoCache = $this->preloadWilayahInfo($berita->items());
                }
            } else {
                // Skip wilayah info jika tidak diminta
                $wilayahInfoCache = [];
            }

            // Transform data to include gambar_url and wilayah_info (dari cache)
            $items = collect($berita->items())->map(function ($item) use ($wilayahInfoCache, $includeWilayah) {
                $wilayahKey = "{$item->province_id}_{$item->districts_id}_{$item->sub_districts_id}_{$item->villages_id}";
                $data = [
                    'id' => $item->id,
                    'judul' => $item->judul,
                    'deskripsi' => $item->deskripsi,
                    'komentar' => $item->komentar,
                    'gambar' => $item->gambar,
                    'gambar_url' => $item->gambar_url, // URL lengkap gambar
                    'user_id' => $item->user_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
                
                // Hanya tambahkan wilayah_info jika diminta
                if ($includeWilayah) {
                    $data['wilayah_info'] = $wilayahInfoCache[$wilayahKey] ?? [];
                }
                
                return $data;
            });

            $response = [
                'status' => 'success',
                'data' => $items,
                'meta' => [
                    'current_page' => $berita->currentPage(),
                    'per_page' => $berita->perPage(),
                    'total' => $berita->total(),
                    'last_page' => $berita->lastPage(),
                ]
            ];

            // Cache hasil secara permanen menggunakan cache store optimal (hanya di-clear saat ada perubahan data)
            $this->cacheStore->forever($cacheKey, $response);
            
            // Simpan cache key ke daftar untuk memudahkan clearing
            $cacheKeysList = $this->cacheStore->get("berita_desa_cache_keys_{$villageId}", []);
            if (!in_array($cacheKey, $cacheKeysList)) {
                $cacheKeysList[] = $cacheKey;
                $this->cacheStore->forever("berita_desa_cache_keys_{$villageId}", $cacheKeysList);
            }

            return response()->json($response, 200);

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

            // Clear cache untuk berita desa
            $this->clearBeritaDesaCache($villageId);

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
     * Pre-load semua wilayah info yang dibutuhkan untuk menghindari API call berulang
     * Menggunakan cache permanen untuk setiap kombinasi wilayah
     */
    private function preloadWilayahInfo($items)
    {
        $wilayahInfoCache = [];
        $uniqueWilayahKeys = [];
        
        // Kumpulkan semua kombinasi wilayah yang unik
        foreach ($items as $item) {
            $key = "{$item->province_id}_{$item->districts_id}_{$item->sub_districts_id}_{$item->villages_id}";
            if (!in_array($key, $uniqueWilayahKeys)) {
                $uniqueWilayahKeys[] = $key;
            }
        }
        
        // Load dari cache atau build untuk setiap kombinasi unik
        foreach ($uniqueWilayahKeys as $key) {
            $cacheKey = "wilayah_info_{$key}";
            
            // Cek cache terlebih dahulu menggunakan cache store optimal
            if ($this->cacheStore->has($cacheKey)) {
                $wilayahInfoCache[$key] = $this->cacheStore->get($cacheKey);
                continue;
            }
            
            // Parse key untuk mendapatkan IDs
            $parts = explode('_', $key);
            $provinceId = $parts[0] ?? null;
            $districtId = $parts[1] ?? null;
            $subDistrictId = $parts[2] ?? null;
            $villageId = $parts[3] ?? null;
            
            // Build wilayah info dengan cache untuk setiap level
            $wilayah = $this->buildWilayahInfoCached($provinceId, $districtId, $subDistrictId, $villageId);
            
            // Cache hasil secara permanen menggunakan cache store optimal
            $this->cacheStore->forever($cacheKey, $wilayah);
            $wilayahInfoCache[$key] = $wilayah;
        }
        
        return $wilayahInfoCache;
    }

    /**
     * Build wilayah info dengan cache untuk setiap level (optimasi)
     * Menggunakan cache store optimal (Redis jika tersedia)
     */
    private function buildWilayahInfoCached($provinceId, $districtId, $subDistrictId, $villageId)
    {
        $wilayah = [];
        
        // Cache provinces (sangat jarang berubah) - cache sekali untuk semua
        if ($provinceId) {
            $provinceCacheKey = "wilayah_province_{$provinceId}";
            if ($this->cacheStore->has($provinceCacheKey)) {
                $wilayah['provinsi'] = $this->cacheStore->get($provinceCacheKey);
            } else {
                try {
                    $provinces = $this->wilayahService->getProvinces();
                    $province = collect($provinces)->firstWhere('id', (int) $provinceId);
                    $provinceName = $province['name'] ?? "Provinsi ID: {$provinceId}";
                    $this->cacheStore->forever($provinceCacheKey, $provinceName);
                    $wilayah['provinsi'] = $provinceName;
                } catch (\Exception $e) {
                    $wilayah['provinsi'] = "Provinsi ID: {$provinceId}";
                }
            }
        }
        
        // Cache kabupaten
        if ($districtId && $provinceId) {
            $districtCacheKey = "wilayah_district_{$districtId}";
            if ($this->cacheStore->has($districtCacheKey)) {
                $wilayah['kabupaten'] = $this->cacheStore->get($districtCacheKey);
            } else {
                try {
                    $provinces = $this->wilayahService->getProvinces();
                    $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                    
                    if ($provinceData && isset($provinceData['code'])) {
                        $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                        $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $districtId);
                        $districtName = $kabupatenData['name'] ?? "Kabupaten ID: {$districtId}";
                        $this->cacheStore->forever($districtCacheKey, $districtName);
                        $wilayah['kabupaten'] = $districtName;
                    } else {
                        $wilayah['kabupaten'] = "Kabupaten ID: {$districtId}";
                    }
                } catch (\Exception $e) {
                    $wilayah['kabupaten'] = "Kabupaten ID: {$districtId}";
                }
            }
        }
        
        // Cache kecamatan
        if ($subDistrictId && $districtId && $provinceId) {
            $subDistrictCacheKey = "wilayah_subdistrict_{$subDistrictId}";
            if ($this->cacheStore->has($subDistrictCacheKey)) {
                $wilayah['kecamatan'] = $this->cacheStore->get($subDistrictCacheKey);
            } else {
                try {
                    $provinces = $this->wilayahService->getProvinces();
                    $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                    
                    if ($provinceData && isset($provinceData['code'])) {
                        $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                        $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $districtId);
                        
                        if ($kabupatenData && isset($kabupatenData['code'])) {
                            $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                            $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $subDistrictId);
                            $subDistrictName = $kecamatanData['name'] ?? "Kecamatan ID: {$subDistrictId}";
                            $this->cacheStore->forever($subDistrictCacheKey, $subDistrictName);
                            $wilayah['kecamatan'] = $subDistrictName;
                        } else {
                            $wilayah['kecamatan'] = "Kecamatan ID: {$subDistrictId}";
                        }
                    } else {
                        $wilayah['kecamatan'] = "Kecamatan ID: {$subDistrictId}";
                    }
                } catch (\Exception $e) {
                    $wilayah['kecamatan'] = "Kecamatan ID: {$subDistrictId}";
                }
            }
        }
        
        // Cache desa
        if ($villageId && $subDistrictId && $districtId && $provinceId) {
            $villageCacheKey = "wilayah_village_{$villageId}";
            if ($this->cacheStore->has($villageCacheKey)) {
                $wilayah['desa'] = $this->cacheStore->get($villageCacheKey);
            } else {
                try {
                    $provinces = $this->wilayahService->getProvinces();
                    $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                    
                    if ($provinceData && isset($provinceData['code'])) {
                        $kabupaten = $this->wilayahService->getKabupaten($provinceData['code']);
                        $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $districtId);
                        
                        if ($kabupatenData && isset($kabupatenData['code'])) {
                            $kecamatan = $this->wilayahService->getKecamatan($kabupatenData['code']);
                            $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $subDistrictId);
                            
                            if ($kecamatanData && isset($kecamatanData['code'])) {
                                $desa = $this->wilayahService->getDesa($kecamatanData['code']);
                                $desaData = collect($desa)->firstWhere('id', (int) $villageId);
                                $villageName = $desaData['name'] ?? "Desa ID: {$villageId}";
                                $this->cacheStore->forever($villageCacheKey, $villageName);
                                $wilayah['desa'] = $villageName;
                            } else {
                                $wilayah['desa'] = "Desa ID: {$villageId}";
                            }
                        } else {
                            $wilayah['desa'] = "Desa ID: {$villageId}";
                        }
                    } else {
                        $wilayah['desa'] = "Desa ID: {$villageId}";
                    }
                } catch (\Exception $e) {
                    $wilayah['desa'] = "Desa ID: {$villageId}";
                }
            }
        }
        
        return $wilayah;
    }

    /**
     * Get wilayah information for a berita (untuk single item, tetap menggunakan cache)
     */
    private function getWilayahInfo($berita)
    {
        $key = "{$berita->province_id}_{$berita->districts_id}_{$berita->sub_districts_id}_{$berita->villages_id}";
        $cacheKey = "wilayah_info_{$key}";
        
        // Cek cache terlebih dahulu menggunakan cache store optimal
        if ($this->cacheStore->has($cacheKey)) {
            return $this->cacheStore->get($cacheKey);
        }
        
        // Build dan cache
        $wilayah = $this->buildWilayahInfoCached(
            $berita->province_id,
            $berita->districts_id,
            $berita->sub_districts_id,
            $berita->villages_id
        );
        
        // Cache hasil secara permanen menggunakan cache store optimal
        $this->cacheStore->forever($cacheKey, $wilayah);
        
        return $wilayah;
    }

    /**
     * Clear cache untuk berita desa berdasarkan village_id
     */
    private function clearBeritaDesaCache($villageId)
    {
        // Simpan daftar cache keys yang perlu di-clear
        $cacheKeysList = $this->cacheStore->get("berita_desa_cache_keys_{$villageId}", []);
        
        // Clear semua cache keys yang tersimpan
        foreach ($cacheKeysList as $key) {
            $this->cacheStore->forget($key);
        }
        
        // Reset daftar cache keys
        $this->cacheStore->forget("berita_desa_cache_keys_{$villageId}");
    }
}

