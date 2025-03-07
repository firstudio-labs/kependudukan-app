<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WilayahController;

Route::prefix('wilayah')->group(function () {
    Route::get('provinsi/{code}/kota', [WilayahController::class, 'getKotaByProvinsi']);
    Route::get('kota/{code}/kecamatan', [WilayahController::class, 'getKecamatanByKota']);
    Route::get('kecamatan/{code}/kelurahan', [WilayahController::class, 'getDesaByKecamatan']);
});
