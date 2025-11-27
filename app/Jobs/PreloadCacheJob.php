<?php

namespace App\Jobs;

use App\Services\VillageContentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PreloadCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type; // 'berita', 'pengumuman', 'agenda', 'laporan'
    protected $villageId;
    protected $cacheStore;

    /**
     * Create a new job instance.
     */
    public function __construct(string $type, int $villageId)
    {
        $this->type = $type;
        $this->villageId = $villageId;
        $this->queue = 'default';
    }

    /**
     * Execute the job.
     */
    public function handle(VillageContentService $contentService)
    {
        try {
            $this->cacheStore = $this->getCacheStore();
            
            // Pre-load cache untuk beberapa kombinasi pagination yang umum
            $commonPerPages = [10, 20, 30];
            $commonPages = [1, 2];
            $commonSearches = [''];
            
            foreach ($commonPerPages as $perPage) {
                foreach ($commonPages as $page) {
                    foreach ($commonSearches as $search) {
                        $cacheKey = "{$this->type}_index_{$this->villageId}_{$search}_{$perPage}_{$page}";
                        
                        // Skip jika sudah ada di cache
                        if ($this->cacheStore->has($cacheKey)) {
                            continue;
                        }

                        $response = $this->buildResponse($contentService, $search, $perPage, $page);
                        if (!$response) {
                            continue;
                        }

                        $this->cacheStore->forever($cacheKey, $response);

                        $cacheKeysList = $this->cacheStore->get("{$this->type}_cache_keys_{$this->villageId}", []);
                        if (!in_array($cacheKey, $cacheKeysList)) {
                            $cacheKeysList[] = $cacheKey;
                            $this->cacheStore->forever("{$this->type}_cache_keys_{$this->villageId}", $cacheKeysList);
                        }

                        Log::info("PreloadCacheJob: cache populated for {$cacheKey}");
                    }
                }
            }
            
            Log::info("PreloadCacheJob completed for {$this->type} village {$this->villageId}");
        } catch (\Exception $e) {
            Log::error("PreloadCacheJob failed for {$this->type} village {$this->villageId}: " . $e->getMessage());
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

    private function buildResponse(VillageContentService $contentService, string $search, int $perPage, int $page): ?array
    {
        switch ($this->type) {
            case 'berita_desa':
            case 'berita':
                return $contentService->buildBeritaIndex($this->villageId, $search, $perPage, $page);
            case 'pengumuman':
                return $contentService->buildPengumumanIndex($this->villageId, $search, $perPage, $page);
            case 'agenda_desa':
            case 'agenda':
                return $contentService->buildAgendaIndex($this->villageId, $search, $perPage, $page);
            case 'laporan_desa':
            case 'laporan':
                return $contentService->buildLaporanIndexForVillage($this->villageId, $search, $perPage, $page);
            default:
                Log::warning("PreloadCacheJob: unknown type {$this->type}");
                return null;
        }
    }
}

