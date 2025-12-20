<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic;
use App\Models\InformasiUsaha;
use App\Observers\InformasiUsahaObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Intervention Image
        $this->app->singleton(ImageManager::class, function () {
            return new ImageManager(['driver' => 'gd']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default timezone to Indonesia
        date_default_timezone_set('Asia/Jakarta');

        // Register model observers
        InformasiUsaha::observe(InformasiUsahaObserver::class);
    }
}
