<?php

namespace App\Jobs;

use App\Services\WilayahService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PreloadWilayahInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $items;
    protected $cacheStore;

    /**
     * Create a new job instance.
     */
    public function __construct(array $items)
    {
        $this->items = $items;
        $this->queue = 'default'; // Gunakan queue default
    }

    /**
     * Execute the job.
     */
    public function handle(WilayahService $wilayahService)
    {
        try {
            $this->cacheStore = $this->getCacheStore();
            
            $uniqueWilayahKeys = [];
            
            // Kumpulkan semua kombinasi wilayah yang unik
            foreach ($this->items as $item) {
                $provinceId = $item['province_id'] ?? null;
                $districtId = $item['districts_id'] ?? null;
                $subDistrictId = $item['sub_districts_id'] ?? null;
                $villageId = $item['villages_id'] ?? null;
                
                $key = "{$provinceId}_{$districtId}_{$subDistrictId}_{$villageId}";
                if (!in_array($key, $uniqueWilayahKeys)) {
                    $uniqueWilayahKeys[] = $key;
                }
            }
            
            // Pre-load semua wilayah info
            foreach ($uniqueWilayahKeys as $key) {
                $cacheKey = "wilayah_info_{$key}";
                
                // Skip jika sudah ada di cache
                if ($this->cacheStore->has($cacheKey)) {
                    continue;
                }
                
                // Parse key untuk mendapatkan IDs
                $parts = explode('_', $key);
                $provinceId = $parts[0] ?? null;
                $districtId = $parts[1] ?? null;
                $subDistrictId = $parts[2] ?? null;
                $villageId = $parts[3] ?? null;
                
                // Build wilayah info dengan cache untuk setiap level
                $wilayah = $this->buildWilayahInfoCached($wilayahService, $provinceId, $districtId, $subDistrictId, $villageId);
                
                // Cache hasil secara permanen
                $this->cacheStore->forever($cacheKey, $wilayah);
            }
            
            Log::info('PreloadWilayahInfoJob completed', [
                'items_count' => count($this->items),
                'unique_keys' => count($uniqueWilayahKeys)
            ]);
        } catch (\Exception $e) {
            Log::error('PreloadWilayahInfoJob failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get cache store - prefer Redis jika tersedia
     */
    private function getCacheStore()
    {
        try {
            if (config('cache.default') === 'redis' || config('cache.stores.redis')) {
                return Cache::store('redis');
            }
        } catch (\Exception $e) {
            Log::warning('Redis tidak tersedia di job, menggunakan default cache: ' . $e->getMessage());
        }
        return Cache::store(config('cache.default', 'file'));
    }

    /**
     * Build wilayah info dengan cache untuk setiap level
     */
    private function buildWilayahInfoCached($wilayahService, $provinceId, $districtId, $subDistrictId, $villageId)
    {
        $wilayah = [];
        
        // Cache provinces
        if ($provinceId) {
            $provinceCacheKey = "wilayah_province_{$provinceId}";
            if ($this->cacheStore->has($provinceCacheKey)) {
                $wilayah['provinsi'] = $this->cacheStore->get($provinceCacheKey);
            } else {
                try {
                    $provinces = $wilayahService->getProvinces();
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
                    $provinces = $wilayahService->getProvinces();
                    $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                    
                    if ($provinceData && isset($provinceData['code'])) {
                        $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
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
                    $provinces = $wilayahService->getProvinces();
                    $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                    
                    if ($provinceData && isset($provinceData['code'])) {
                        $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
                        $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $districtId);
                        
                        if ($kabupatenData && isset($kabupatenData['code'])) {
                            $kecamatan = $wilayahService->getKecamatan($kabupatenData['code']);
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
                    $provinces = $wilayahService->getProvinces();
                    $provinceData = collect($provinces)->firstWhere('id', (int) $provinceId);
                    
                    if ($provinceData && isset($provinceData['code'])) {
                        $kabupaten = $wilayahService->getKabupaten($provinceData['code']);
                        $kabupatenData = collect($kabupaten)->firstWhere('id', (int) $districtId);
                        
                        if ($kabupatenData && isset($kabupatenData['code'])) {
                            $kecamatan = $wilayahService->getKecamatan($kabupatenData['code']);
                            $kecamatanData = collect($kecamatan)->firstWhere('id', (int) $subDistrictId);
                            
                            if ($kecamatanData && isset($kecamatanData['code'])) {
                                $desa = $wilayahService->getDesa($kecamatanData['code']);
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
}

