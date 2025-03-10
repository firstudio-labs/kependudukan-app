<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\BiodataController;


// API routes for location data
Route::get('districts/{code}', [BiodataController::class, 'getCities']);
Route::get('/sub-districts/{code}', [BiodataController::class, 'getDistricts']);
Route::get('/villages/{code}', [BiodataController::class, 'getVillages']);


