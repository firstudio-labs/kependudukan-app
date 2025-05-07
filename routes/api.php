<?php

use App\Http\Controllers\Api\JenisAsetController;
use App\Http\Controllers\Api\KlasifikasiController;
use App\Http\Controllers\Api\LaporanDesaController;
use App\Http\Controllers\Api\LaporDesaApiController;
use App\Http\Controllers\Api\LaporDesaController;
use App\Http\Middleware\ApiTokenOwnerMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KelolaAsetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;

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


Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'homepage'])->name('homepage');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(ApiTokenOwnerMiddleware::class)->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/token-info', function (Request $request) {
        return response()->json([
            'token_owner' => [
                'id' => $request->attributes->get('token_owner')->id,
                'nik' => $request->attributes->get('token_owner')->nik,
                'type' => $request->attributes->get('token_owner_type'),
                'role' => $request->attributes->get('token_owner_role'),
            ],
            'token' => [
                'id' => $request->attributes->get('token')->id,
                'name' => $request->attributes->get('token')->name,
                'last_used_at' => $request->attributes->get('token')->last_used_at,
            ]
        ]);
    });

    //List Aset
    Route::get('/klasifikasi', [KlasifikasiController::class, 'index']);
    Route::get('/jenis-aset', [JenisAsetController::class, 'index']);
    



    Route::prefix('user')->group(function () {
        //kelola aset
        Route::get('/kelola-aset', [KelolaAsetController::class, 'index'])
            ->name('user.kelola-aset.index');
        Route::get('/kelola-aset/create', [KelolaAsetController::class, 'create'])
            ->name('user.kelola-aset.create');
        Route::post('/kelola-aset', [KelolaAsetController::class, 'store'])
            ->name('user.kelola-aset.store');
        Route::get('/kelola-aset/{id}/edit', [KelolaAsetController::class, 'edit'])
            ->name('user.kelola-aset.edit');
        Route::put('/kelola-aset/{id}', [KelolaAsetController::class, 'update'])
            ->name('user.kelola-aset.update');
        Route::delete('/kelola-aset/{id}', [KelolaAsetController::class, 'destroy'])
            ->name('user.kelola-aset.destroy');

        //laporan desa
        Route::get('/laporan-desa', [LaporanDesaController::class, 'index'])
            ->name('user.laporan-desa.index');
        Route::get('/laporan-desa/create', [LaporanDesaController::class, 'create'])
            ->name('user.laporan-desa.create');
        Route::post('/laporan-desa', [LaporanDesaController::class, 'store'])
            ->name('user.laporan-desa.store');
        Route::get('/laporan-desa/{id}/edit', [LaporanDesaController::class, 'edit'])
            ->name('user.laporan-desa.edit');
        Route::put('/laporan-desa/{id}', [LaporanDesaController::class, 'update'])
            ->name('user.laporan-desa.update');
        Route::delete('/laporan-desa/{id}', [LaporanDesaController::class, 'destroy'])
            ->name('user.laporan-desa.destroy');

        //profile
        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('user.profile.index');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])
            ->name('user.profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('user.profile.update');
        Route::get('/profile/create', [ProfileController::class, 'create'])
            ->name('user.profile.create');
        Route::post('/profile', [ProfileController::class, 'store'])
            ->name('user.profile.store');
        Route::post('/profile/update-location', [ProfileController::class, 'updateLocation'])
            ->name('user.profile.updateLocation');

        Route::get('/family-member/{nik}/documents', [ProfileController::class, 'getFamilyMemberDocuments'])
            ->name('user.family-member.documents');
        Route::post('/family-member/{nik}/upload-document', [ProfileController::class, 'uploadFamilyMemberDocument'])
            ->name('user.family-member.upload-document');
        Route::get('/family-member/{nik}/document/{documentType}/view', [ProfileController::class, 'viewFamilyMemberDocument'])
            ->name('user.family-member.view-document');
        Route::delete('/family-member/{nik}/delete-document/{documentType}', [ProfileController::class, 'deleteFamilyMemberDocument'])
            ->name('user.family-member.delete-document');
    });
});






Route::fallback(function () {
    return response()->json(['message' => 'API endpoint not found'], 404);
});

