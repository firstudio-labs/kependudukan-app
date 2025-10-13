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
use App\Http\Controllers\Api\PengumumanController as ApiPengumumanController;
use App\Http\Controllers\Api\AgendaDesaController;
use App\Http\Controllers\Api\WarungkuController as ApiWarungkuController;
use App\Http\Controllers\Api\PemerintahDesaController;
use App\Http\Controllers\Api\TagihanController as ApiTagihanController;
use App\Http\Controllers\Api\AdminTagihanController;

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
    // Admin login (users table)
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');
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
        Route::post('/profile/update-phone', [ProfileController::class, 'updatePhone'])
            ->name('user.profile.update-phone');
        Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])
            ->name('user.profile.update-password');
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
        Route::post('/berita-desa', [BeritaDesaController::class, 'store'])
            ->name('user.berita-desa.store');
        Route::post('/berita-desa/{id}/send-approval', [BeritaDesaController::class, 'sendApproval'])
            ->name('user.berita-desa.send-approval');
        Route::get('/berita-desa/{id}', [BeritaDesaController::class, 'show'])
            ->name('user.berita-desa.show');

        // pengumuman
        Route::get('/pengumuman', [ApiPengumumanController::class, 'index'])
            ->name('user.pengumuman.index');
        Route::get('/pengumuman/{id}', [ApiPengumumanController::class, 'show'])
            ->name('user.pengumuman.show');

        // agenda desa
        Route::get('/agenda-desa', [AgendaDesaController::class, 'index'])
            ->name('user.agenda-desa.index');
        Route::get('/agenda-desa/{id}', [AgendaDesaController::class, 'show'])
            ->name('user.agenda-desa.show');

        // Warungku API
        Route::get('/warungku', [ApiWarungkuController::class, 'index'])->name('user.api.warungku.index');
        Route::get('/warungku/my', [ApiWarungkuController::class, 'my'])->name('user.api.warungku.my');
        Route::post('/warungku', [ApiWarungkuController::class, 'store'])->name('user.api.warungku.store');
        Route::get('/warungku/{barangWarungku}', [ApiWarungkuController::class, 'show'])->name('user.api.warungku.show');
        Route::get('/warungku/{barangWarungku}/edit', [ApiWarungkuController::class, 'edit'])->name('user.api.warungku.edit');
        Route::post('/warungku/{barangWarungku}', [ApiWarungkuController::class, 'update'])->name('user.api.warungku.update');
        Route::delete('/warungku/{barangWarungku}', [ApiWarungkuController::class, 'destroy'])->name('user.api.warungku.destroy');

        // Warungku dropdown filters
        Route::get('/warungku/filters', [ApiWarungkuController::class, 'filters'])->name('user.api.warungku.filters');
        Route::get('/warungku/filters/klasifikasi', [ApiWarungkuController::class, 'klasifikasiList'])->name('user.api.warungku.filters.klasifikasi');
        Route::get('/warungku/filters/jenis', [ApiWarungkuController::class, 'jenisByKlasifikasi'])->name('user.api.warungku.filters.jenis');

        // Wilayah options for filters
        Route::get('/warungku/wilayah/provinces', [ApiWarungkuController::class, 'wilayahProvinces'])->name('user.api.warungku.wilayah.provinces');
        Route::get('/warungku/wilayah/districts/{provinceCode}', [ApiWarungkuController::class, 'wilayahDistricts'])->name('user.api.warungku.wilayah.districts');
        Route::get('/warungku/wilayah/sub-districts/{districtCode}', [ApiWarungkuController::class, 'wilayahSubDistricts'])->name('user.api.warungku.wilayah.sub_districts');
        Route::get('/warungku/wilayah/villages/{subDistrictCode}', [ApiWarungkuController::class, 'wilayahVillages'])->name('user.api.warungku.wilayah.villages');

        // Pemerintah Desa (berdasarkan desa user login)
        Route::get('/pemerintah-desa', [PemerintahDesaController::class, 'show'])->name('user.api.pemerintah-desa.show');

        // Tagihan penduduk (berdasarkan NIK user)
        Route::get('/tagihan', [ApiTagihanController::class, 'index'])->name('user.api.tagihan.index');
        Route::get('/tagihan/{tagihan}', [ApiTagihanController::class, 'show'])->name('user.api.tagihan.show');
        Route::get('/tagihan-kategori', [ApiTagihanController::class, 'kategori'])->name('user.api.tagihan.kategori');
        // Admin Desa - Kelola Tagihan (berdasarkan desa user admin)
        Route::prefix('admin/tagihan')->group(function () {
            Route::get('/', [AdminTagihanController::class, 'index'])->name('admin.api.tagihan.index');
            Route::post('/', [AdminTagihanController::class, 'store'])->name('admin.api.tagihan.store');
            Route::get('/{tagihan}', [AdminTagihanController::class, 'show'])->name('admin.api.tagihan.show');
            Route::put('/{tagihan}', [AdminTagihanController::class, 'update'])->name('admin.api.tagihan.update');
            Route::delete('/{tagihan}', [AdminTagihanController::class, 'destroy'])->name('admin.api.tagihan.destroy');
            Route::post('/{tagihan}/status', [AdminTagihanController::class, 'updateStatus'])->name('admin.api.tagihan.update-status');
            Route::get('/kategori', [AdminTagihanController::class, 'kategori'])->name('admin.api.tagihan.kategori');
            Route::get('/kategori/{kategoriId}/sub', [AdminTagihanController::class, 'subKategoriByKategori'])->name('admin.api.tagihan.sub-kategori');
        });


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