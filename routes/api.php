<?php

use App\Http\Controllers\Api\BeritaDesaController;
use App\Http\Controllers\Api\JenisAsetController;
use App\Http\Controllers\Api\KlasifikasiController;
use App\Http\Controllers\Api\LaporanDesaController;
use App\Http\Controllers\Api\LaporDesaController;
use App\Http\Controllers\Api\RiwayatSuratController;
use App\Http\Middleware\ApiTokenOwnerMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KelolaAsetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\BiodataController;
use App\Http\Controllers\Api\AdminBiodataApprovalController;

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
    //homepage
    Route::get('/', [AuthController::class, 'homepage'])->name('homepage');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

//logout
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

    //Superadmin list
    Route::get('/klasifikasi', [KlasifikasiController::class, 'index']);
    Route::get('/jenis-aset', [JenisAsetController::class, 'index']);
    Route::get('/lapor-desa', [LaporDesaController::class, 'index']);

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

        //berita desa
        Route::get('/berita-desa', [BeritaDesaController::class, 'index'])
            ->name('user.berita-desa.index');
        Route::get('/berita-desa/{id}', [BeritaDesaController::class, 'show'])
            ->name('user.berita-desa.show');


        //riwayat surat
        Route::get('/riwayat-surat', [RiwayatSuratController::class, 'index'])
            ->name('user.riwayat-surat.index');
        Route::get('/riwayat-surat/skck/{id}/detail', [RiwayatSuratController::class, 'showSKCK'])
            ->name('user.riwayat-surat.skck.detail');
        Route::get('/riwayat-surat/administrasi/{id}/detail', [RiwayatSuratController::class, 'showAdministrasi'])
            ->name('user.riwayat-surat.administrasi.detail');
        Route::get('/riwayat-surat/domisili/{id}/detail', [RiwayatSuratController::class, 'showDomisili'])
            ->name('user.riwayat-surat.domisili.detail');
        Route::get('/riwayat-surat/domisili-usaha/{id}/detail', [RiwayatSuratController::class, 'showDomisiliUsaha'])
            ->name('user.riwayat-surat.domisili-usaha.detail');
        Route::get('/riwayat-surat/kehilangan/{id}/detail', [RiwayatSuratController::class, 'showKehilangan'])
            ->name('user.riwayat-surat.kehilangan.detail');
        Route::get('/riwayat-surat/ktp/{id}/detail', [RiwayatSuratController::class, 'showKTP'])
            ->name('user.riwayat-surat.ktp.detail');
        Route::get('/riwayat-surat/rumah-sewa/{id}/detail', [RiwayatSuratController::class, 'showRumahSewa'])
            ->name('user.riwayat-surat.rumah-sewa.detail');
        Route::get('/riwayat-surat/keramaian/{id}/detail', [RiwayatSuratController::class, 'showKeramaian'])
            ->name('user.riwayat-surat.keramaian.detail');
        Route::get('/riwayat-surat/kelahiran/{id}/detail', [RiwayatSuratController::class, 'showKelahiran'])
            ->name('user.riwayat-surat.kelahiran.detail');
        Route::get('/riwayat-surat/kematian/{id}/detail', [RiwayatSuratController::class, 'showKematian'])
            ->name('user.riwayat-surat.kematian.detail');
        Route::get('/riwayat-surat/ahli-waris/{id}/detail', [RiwayatSuratController::class, 'showAhliWaris'])
            ->name('user.riwayat-surat.ahli-waris.detail');


    });
});

// Versi sederhana tanpa middleware kustom, gunakan auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Penduduk kirim form perubahan biodata (simple)
    Route::get('/biodata', [BiodataController::class, 'getBiodata']);
    Route::post('/biodata/request-update', [BiodataController::class, 'requestUpdate']);
    Route::get('/biodata/history', [BiodataController::class, 'getHistory']);
    Route::get('/biodata/request/{requestId}', [BiodataController::class, 'getRequestDetail']);
    Route::delete('/biodata/request/{requestId}', [BiodataController::class, 'cancelRequest']);

    // Admin desa approval (simple)
    Route::prefix('admin/biodata-approval')->group(function () {
        Route::get('/pending', [AdminBiodataApprovalController::class, 'getPendingRequests']);
        Route::get('/request/{requestId}', [AdminBiodataApprovalController::class, 'getRequestDetail']);
        Route::post('/request/{requestId}/approve', [AdminBiodataApprovalController::class, 'approve']);
        Route::post('/request/{requestId}/reject', [AdminBiodataApprovalController::class, 'reject']);
    });
});

Route::fallback(function () {
    return response()->json(['message' => 'API endpoint not found'], 404);
});