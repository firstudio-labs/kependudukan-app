<?php

namespace App\Jobs;

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
    public function handle()
    {
        try {
            $this->cacheStore = $this->getCacheStore();
            
            // Pre-load cache untuk beberapa kombinasi pagination yang umum
            $commonPerPages = [10, 20, 30];
            $commonPages = [1, 2];
            $commonSearches = ['', 'search'];
            
            foreach ($commonPerPages as $perPage) {
                foreach ($commonPages as $page) {
                    foreach ($commonSearches as $search) {
                        $cacheKey = "{$this->type}_index_{$this->villageId}_{$search}_{$perPage}_{$page}";
                        
                        // Skip jika sudah ada di cache
                        if ($this->cacheStore->has($cacheKey)) {
                            continue;
                        }
                        
                        // Dispatch job untuk load data (akan di-handle oleh controller logic)
                        // Atau bisa langsung load di sini jika perlu
                        Log::info("PreloadCacheJob: Pre-loading cache for {$cacheKey}");
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
}

