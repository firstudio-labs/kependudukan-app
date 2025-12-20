<?php

namespace App\Observers;

use App\Models\InformasiUsaha;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InformasiUsahaObserver
{
    /**
     * Handle the InformasiUsaha "created" event.
     */
    public function created(InformasiUsaha $informasiUsaha): void
    {
        $this->invalidateCache($informasiUsaha);
    }

    /**
     * Handle the InformasiUsaha "updated" event.
     */
    public function updated(InformasiUsaha $informasiUsaha): void
    {
        $this->invalidateCache($informasiUsaha);
    }

    /**
     * Handle the InformasiUsaha "deleted" event.
     */
    public function deleted(InformasiUsaha $informasiUsaha): void
    {
        $this->invalidateCache($informasiUsaha);
    }

    /**
     * Invalidate cache terkait InformasiUsaha
     */
    private function invalidateCache(InformasiUsaha $informasiUsaha): void
    {
        try {
            // Invalidate cache berdasarkan penduduk_id
            if ($informasiUsaha->penduduk_id) {
                $cacheKey = "informasi_usaha_user_{$informasiUsaha->penduduk_id}";
                Cache::forget($cacheKey);
                Log::info("Cache invalidated: {$cacheKey}");
            }

            // Invalidate cache berdasarkan KK
            if ($informasiUsaha->kk) {
                $cacheKey = "informasi_usaha_kk_{$informasiUsaha->kk}";
                Cache::forget($cacheKey);
                Log::info("Cache invalidated: {$cacheKey}");
            }

            // Juga invalidate cache untuk penduduk_id dan KK dari data lama (jika ada perubahan)
            if ($informasiUsaha->wasChanged('penduduk_id') && $informasiUsaha->getOriginal('penduduk_id')) {
                $oldCacheKey = "informasi_usaha_user_{$informasiUsaha->getOriginal('penduduk_id')}";
                Cache::forget($oldCacheKey);
                Log::info("Cache invalidated (old penduduk_id): {$oldCacheKey}");
            }

            if ($informasiUsaha->wasChanged('kk') && $informasiUsaha->getOriginal('kk')) {
                $oldCacheKey = "informasi_usaha_kk_{$informasiUsaha->getOriginal('kk')}";
                Cache::forget($oldCacheKey);
                Log::info("Cache invalidated (old kk): {$oldCacheKey}");
            }
        } catch (\Exception $e) {
            Log::error('Error invalidating cache for InformasiUsaha: ' . $e->getMessage());
        }
    }
}

