<?php

namespace App\Services;

use App\Models\AgendaDesa;
use App\Models\BeritaDesa;
use App\Models\LaporanDesa;
use App\Models\Pengumuman;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VillageContentService
{
    protected WilayahService $wilayahService;
    protected $cacheStore;

    public function __construct(WilayahService $wilayahService)
    {
        $this->wilayahService = $wilayahService;
        $this->cacheStore = $this->getCacheStore();
    }

    /** @return array{status:string,data:array,meta:array} */
    public function buildBeritaIndex(int $villageId, string $search, int $perPage, int $page): array
    {
        $query = BeritaDesa::query()
            ->select([
                'id', 'judul', 'deskripsi', 'komentar', 'gambar',
                'user_id', 'villages_id', 'province_id', 'districts_id',
                'sub_districts_id', 'created_at', 'updated_at'
            ])
            ->where('villages_id', $villageId)
            ->where('status', 'published');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        /** @var LengthAwarePaginator $berita */
        $berita = $query->latest()->paginate($perPage, ['*'], 'page', $page);
        $wilayahCache = $this->preloadWilayahInfo($berita->items());

        $items = collect($berita->items())->map(function ($item) use ($wilayahCache) {
            $wilayahKey = "{$item->province_id}_{$item->districts_id}_{$item->sub_districts_id}_{$item->villages_id}";
            return [
                'id' => $item->id,
                'judul' => $item->judul,
                'deskripsi' => $item->deskripsi,
                'komentar' => $item->komentar,
                'gambar' => $item->gambar,
                'gambar_url' => $item->gambar_url,
                'user_id' => $item->user_id,
                'wilayah_info' => $wilayahCache[$wilayahKey] ?? [],
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return [
            'status' => 'success',
            'data' => $items,
            'meta' => [
                'current_page' => $berita->currentPage(),
                'per_page' => $berita->perPage(),
                'total' => $berita->total(),
                'last_page' => $berita->lastPage(),
            ],
        ];
    }

    /** @return array{status:string,data:array,meta:array} */
    public function buildPengumumanIndex(int $villageId, string $search, int $perPage, int $page): array
    {
        $query = Pengumuman::query()
            ->select(['id', 'judul', 'deskripsi', 'gambar', 'villages_id', 'created_at', 'updated_at'])
            ->where('villages_id', $villageId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        return [
            'status' => 'success',
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ];
    }

    /** @return array{status:string,data:array,meta:array} */
    public function buildAgendaIndex(int $villageId, string $search, int $perPage, int $page): array
    {
        $query = AgendaDesa::query()
            ->select(['id', 'judul', 'deskripsi', 'alamat', 'tag_lokasi', 'gambar', 'villages_id', 'created_at', 'updated_at'])
            ->where('villages_id', $villageId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate($perPage, ['*'], 'page', $page);

        return [
            'status' => 'success',
            'data' => $items->items(),
            'meta' => [
                'current_page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage(),
            ],
        ];
    }

    /** @return array{status:string,data:LengthAwarePaginator} */
    public function buildLaporanIndexForVillage(int $villageId, string $search, int $perPage, int $page): array
    {
        $query = LaporanDesa::query()
            ->select([
                'id', 'user_id', 'village_id', 'lapor_desa_id', 'judul_laporan',
                'deskripsi_laporan', 'gambar', 'lokasi', 'tag_lokasi', 'status',
                'created_at', 'updated_at'
            ])
            ->where('village_id', $villageId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_laporan', 'like', "%{$search}%")
                    ->orWhere('deskripsi_laporan', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        return [
            'status' => 'success',
            'data' => $items,
        ];
    }

    /**
     * Preload wilayah info for collection of models.
     * @param array|Collection $items
     */
    public function preloadWilayahInfo($items): array
    {
        $wilayahInfoCache = [];
        $uniqueKeys = [];

        foreach ($items as $item) {
            $key = "{$item->province_id}_{$item->districts_id}_{$item->sub_districts_id}_{$item->villages_id}";
            if (!in_array($key, $uniqueKeys)) {
                $uniqueKeys[] = $key;
            }
        }

        foreach ($uniqueKeys as $key) {
            $cacheKey = "wilayah_info_{$key}";
            if ($this->cacheStore->has($cacheKey)) {
                $wilayahInfoCache[$key] = $this->cacheStore->get($cacheKey);
                continue;
            }

            $parts = explode('_', $key);
            $wilayah = $this->buildWilayahInfoCached(
                $parts[0] ?? null,
                $parts[1] ?? null,
                $parts[2] ?? null,
                $parts[3] ?? null
            );

            $this->cacheStore->forever($cacheKey, $wilayah);
            $wilayahInfoCache[$key] = $wilayah;
        }

        return $wilayahInfoCache;
    }

    private function buildWilayahInfoCached($provinceId, $districtId, $subDistrictId, $villageId): array
    {
        $wilayah = [];

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

    private function getCacheStore()
    {
        try {
            if (config('cache.default') === 'redis' || config('cache.stores.redis')) {
                return Cache::store('redis');
            }
        } catch (\Exception $e) {
            Log::warning('Redis tidak tersedia di service, menggunakan default cache: ' . $e->getMessage());
        }
        return Cache::store(config('cache.default', 'file'));
    }
}


