<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Biodata API Routes (for mobile app users)
Route::prefix('biodata')->middleware('auth:sanctum')->group(function () {
    Route::get('/', 'App\Http\Controllers\Api\BiodataController@getBiodata');
    Route::post('/request-update', 'App\Http\Controllers\Api\BiodataController@requestUpdate');
    Route::get('/history', 'App\Http\Controllers\Api\BiodataController@getHistory');
    Route::get('/request/{requestId}', 'App\Http\Controllers\Api\BiodataController@getRequestDetail');
    Route::delete('/request/{requestId}', 'App\Http\Controllers\Api\BiodataController@cancelRequest');
});

// Admin Biodata Approval API Routes (for admin desa)
Route::prefix('admin/biodata-approval')->middleware('auth:sanctum')->group(function () {
    Route::get('/pending', 'App\Http\Controllers\Api\AdminBiodataApprovalController@getPendingRequests');
    Route::get('/request/{requestId}', 'App\Http\Controllers\Api\AdminBiodataApprovalController@getRequestDetail');
    Route::post('/request/{requestId}/approve', 'App\Http\Controllers\Api\AdminBiodataApprovalController@approve');
    Route::post('/request/{requestId}/reject', 'App\Http\Controllers\Api\AdminBiodataApprovalController@reject');
});

