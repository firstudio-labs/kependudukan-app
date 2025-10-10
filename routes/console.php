<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Penjadwalan generate tagihan bulanan setiap tanggal 1 pukul 01:00
Schedule::command('app:generate-monthly-tagihan')->monthlyOn(1, '1:00');
