<?php

use App\Http\Controllers\adminDesa\ProfileDesaController;
use App\Http\Controllers\adminDesa\BiodataApprovalController as AdminDesaBiodataApprovalController;
use App\Http\Controllers\BeritaDesaController;
use App\Http\Controllers\adminDesa\BeritaDesaController as AdminDesaBeritaDesaController;
use App\Http\Controllers\adminDesa\PengumumanController as AdminDesaPengumumanController;
use App\Http\Controllers\User\RiwayatSuratController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataKKController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\BiodataController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Surat\AdministrasiController;
use App\Http\Controllers\Surat\KehilanganController;
use App\Http\Controllers\Surat\DomisiliController;
use App\Http\Controllers\Surat\SKCKController;
use App\Http\Controllers\Surat\DomisiliUsahaController;
use App\Http\Controllers\Surat\AhliWarisController;
use App\Http\Controllers\Surat\KelahiranController;
use App\Http\Controllers\Surat\KematianController;
use App\Http\Controllers\Surat\IzinKeramaianController;
use App\Http\Controllers\Surat\RumahSewaController;
use App\Http\Controllers\Surat\PengantarKtpController;
use App\Http\Controllers\PenandatangananController;
use App\Http\Controllers\KlasifikasiController;
use App\Http\Controllers\JenisAsetController;
use App\Http\Controllers\KelolaAsetController;
use App\Http\Controllers\guest\PelayananController;
use App\Http\Controllers\guest\AdministrasiUmumController;
use App\Http\Controllers\guest\KehilanganSuratController;
use App\Http\Controllers\guest\SKCKSuratController;
use App\Http\Controllers\superadmin\KeperluanController;
use App\Http\Controllers\guest\DomisiliSuratController;
use App\Http\Controllers\guest\DomisiliUsahaSuratController;
use App\Http\Controllers\guest\KematianSuratController;
use App\Http\Controllers\guest\KeramaianSuratController;
use App\Http\Controllers\guest\KTPSuratController;
use App\Http\Controllers\guest\KelahiranSuratController;
use App\Http\Controllers\guest\AhliWarisSuratController;
use App\Http\Controllers\guest\RumahSewaSuratController;
use App\Http\Controllers\adminKabupaten\ProfileKabController;
use App\Http\Controllers\LaporanDesaController;
use App\Http\Controllers\LaporDesaController;
use App\Http\Controllers\adminDesa\LaporanDesaController as AdminDesaLaporanDesaController;
use App\Http\Controllers\guest\BukuTamuController;
use App\Http\Controllers\superadmin\BeritaDesaController as SuperadminBeritaDesaController;
use App\Http\Controllers\User\BeritaDesaController as UserBeritaDesaController;
use App\Http\Controllers\User\PengumumanController as UserPengumumanController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\User\BiodataChangeController as UserBiodataChangeController;


// Homepage route should use our new method to force logout
Route::get('/', [AuthController::class, 'homepage'])->name('homepage');

// Guest accessible routes
Route::get('/pelayanan', [PelayananController::class, 'index'])->name('guest.pelayanan.index');
Route::post('/pelayanan', [PelayananController::class, 'store'])->name('guest.pelayanan.store');
// Update this route to accept location parameters
Route::get('/pelayanan/list/{province_id}/{district_id}/{sub_district_id}/{village_id}', [PelayananController::class, 'list'])->name('guest.pelayanan.list');
Route::get('/pelayanan/surat/{id}', [PelayananController::class, 'showSuratForm'])->name('guest.pelayanan.surat');
Route::get('/pelayanan/antrian/{id}', [PelayananController::class, 'showAntrian'])->name('guest.pelayanan.antrian');
Route::get('/pelayanan/buku-tamu/{province_id}/{district_id}/{sub_district_id}/{village_id}', [BukuTamuController::class, 'index'])->name('guest.buku-tamu');
Route::post('/pelayanan/buku-tamu', [BukuTamuController::class, 'store'])->name('guest.buku-tamu.store');
Route::prefix('pelayanan')->name('guest.')->group(function () {
    // Update route to accept location parameters
    Route::get('/administrasi', [AdministrasiUmumController::class, 'index'])->name('surat.administrasi');
    Route::post('/administrasi', [AdministrasiUmumController::class, 'store'])->name('surat.administrasi.store');

    // Do this for all other service routes
    Route::get('/kehilangan', [KehilanganSuratController::class, 'index'])->name('surat.kehilangan');
    Route::post('/kehilangan', [KehilanganSuratController::class, 'store'])->name('surat.kehilangan.store');

    // Add SKCK routes
    Route::get('/skck', [SKCKSuratController::class, 'index'])->name('surat.skck');
    Route::post('/skck', [SKCKSuratController::class, 'store'])->name('surat.skck.store');

    // Add Domisili routes
    Route::get('/domisili', [DomisiliSuratController::class, 'index'])->name('surat.domisili');
    Route::post('/domisili', [DomisiliSuratController::class, 'store'])->name('surat.domisili.store');

    // Add Domisili Usaha routes
    Route::get('/domisili-usaha', [DomisiliUsahaSuratController::class, 'index'])->name('surat.domisili-usaha');
    Route::post('/domisili-usaha', [DomisiliUsahaSuratController::class, 'store'])->name('surat.domisili-usaha.store');

    // Add Kematian routes
    Route::get('/kematian', [KematianSuratController::class, 'index'])->name('surat.kematian');
    Route::post('/kematian', [KematianSuratController::class, 'store'])->name('surat.kematian.store');

    // Add Keramaian routes
    Route::get('/keramaian', [KeramaianSuratController::class, 'index'])->name('surat.keramaian');
    Route::post('/keramaian', [KeramaianSuratController::class, 'store'])->name('surat.keramaian.store');

    // Add KTP routes
    Route::get('/ktp', [KTPSuratController::class, 'index'])->name('surat.ktp');
    Route::post('/ktp', [KTPSuratController::class, 'store'])->name('surat.ktp.store');

    // Add Kelahiran (Birth) routes
    Route::get('/kelahiran', [KelahiranSuratController::class, 'index'])->name('surat.kelahiran');
    Route::post('/kelahiran', [KelahiranSuratController::class, 'store'])->name('surat.kelahiran.store');

    // Add Ahli Waris (Inheritance) routes
    Route::get('/ahli-waris', [AhliWarisSuratController::class, 'index'])->name('surat.ahli-waris');
    Route::post('/ahli-waris', [AhliWarisSuratController::class, 'store'])->name('surat.ahli-waris.store');

    // Add Rumah Sewa (Rental House) routes
    Route::get('/rumah-sewa', [RumahSewaSuratController::class, 'index'])->name('surat.rumah-sewa');
    Route::post('/rumah-sewa', [RumahSewaSuratController::class, 'store'])->name('surat.rumah-sewa.store');
});
// Rute Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



// Route untuk superadmin - menggunakan web guard
Route::middleware(['auth:web', 'role:superadmin'])->group(function () {
    Route::get('/superadmin/index', [DashboardController::class, 'index'])->name('superadmin.index');
    Route::get('/superadmin/biodata/index', [BiodataController::class, 'index'])->name('superadmin.biodata.index');
    Route::get('/superadmin/biodata/create', [BiodataController::class, 'create'])->name('superadmin.biodata.create');
    Route::post('/superadmin/biodata/store', [BiodataController::class, 'store'])->name('superadmin.biodata.store');
    Route::delete('/superadmin/biodata/{id}', [BiodataController::class, 'destroy'])->name('superadmin.biodata.destroy');
    Route::get('/superadmin/biodata/{nik}/edit', [BiodataController::class, 'edit'])->name('superadmin.biodata.edit');
    Route::put('/superadmin/biodata/{nik}', [BiodataController::class, 'update'])->name('superadmin.biodata.update');
    Route::get('/superadmin/datakk/index', [DataKKController::class, 'index'])->name('superadmin.datakk.index');
    Route::get('/superadmin/masterdata/kk', [DataKKController::class, 'masterdata'])->name('superadmin.masterdata.kk');
    Route::get('/superadmin/datakk/create', [DataKKController::class, 'create'])->name('superadmin.datakk.create');
    Route::post('/kk', [DataKKController::class, 'store'])->name('kk.store');
    Route::get('/superadmin/datakk/{id}/edit', [DataKKController::class, 'edit'])->name('superadmin.datakk.update');
    Route::put('/kk/{id}', [DataKKController::class, 'update'])->name('kk.update');
    Route::delete('/datakk/{id}', [DataKKController::class, 'destroy'])->name('superadmin.destroy');
    Route::get('/superadmin/datamaster/job/index', [JobController::class, 'index'])->name('superadmin.datamaster.job.index');
    Route::get('/superadmin/datamaster/job/create', [JobController::class, 'create'])->name('superadmin.datamaster.job.create');
    Route::post('/superadmin/datamaster/job', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/superadmin/datamaster/job/{id}/edit', [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('/superadmin/datamaster/job/{id}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/superadmin/datamaster/job/{id}', [JobController::class, 'destroy'])->name('superadmin.datamaster.job.destroy');
    Route::get('/superadmin/datamaster/wilayah/provinsi/index', [WilayahController::class, 'showProvinsi'])->name('superadmin.datamaster.wilayah.provinsi.index');
    Route::get('/superadmin/datamaster/wilayah/kabupaten/index', [WilayahController::class, 'showKabupaten'])->name('superadmin.datamaster.wilayah.kabupaten.index');
    Route::get('/superadmin/datamaster/wilayah/kecamatan/index', [WilayahController::class, 'showKecamatan'])->name('superadmin.datamaster.wilayah.kecamatan.index');
    Route::get('/superadmin/datamaster/wilayah/desa/index', [WilayahController::class, 'showDesa'])->name('superadmin.datamaster.wilayah.desa.index');

    // Add the new route for storing family members
    Route::post('/superadmin/datakk/store-family-member', [DataKKController::class, 'storeFamilyMember'])->name('superadmin.datakk.store-family-member');

    // Administrasi routes
    Route::get('/superadmin/surat/administrasi/index', [AdministrasiController::class, 'index'])
        ->name('superadmin.surat.administrasi.index');
    Route::get('/superadmin/surat/administrasi/create', [AdministrasiController::class, 'create'])
        ->name('superadmin.surat.administrasi.create');
    Route::post('/superadmin/surat/administrasi', [AdministrasiController::class, 'store'])
        ->name('superadmin.surat.administrasi.store');
    Route::get('/superadmin/surat/administrasi/{id}/detail', [AdministrasiController::class, 'show'])
        ->name('superadmin.surat.administrasi.show');
    Route::get('/superadmin/surat/administrasi/{id}/edit', [AdministrasiController::class, 'edit'])
        ->name('superadmin.surat.administrasi.edit');
    Route::put('/superadmin/surat/administrasi/{id}', [AdministrasiController::class, 'update'])
        ->name('superadmin.surat.administrasi.update');
    Route::delete('/superadmin/surat/administrasi/{id}', [AdministrasiController::class, 'destroy'])
        ->name('superadmin.surat.administrasi.delete');
    Route::get('/superadmin/surat/administrasi/{id}/pdf', [AdministrasiController::class, 'generatePDF'])
        ->name('superadmin.surat.administrasi.pdf');

    // Kehilangan routes
    Route::get('/superadmin/surat/kehilangan/index', [KehilanganController::class, 'index'])
        ->name('superadmin.surat.kehilangan.index');
    Route::get('/superadmin/surat/kehilangan/create', [KehilanganController::class, 'create'])
        ->name('superadmin.surat.kehilangan.create');
    Route::post('/superadmin/surat/kehilangan', [KehilanganController::class, 'store'])
        ->name('superadmin.surat.kehilangan.store');
    Route::get('/superadmin/surat/kehilangan/{id}/detail', [KehilanganController::class, 'show'])
        ->name('superadmin.surat.kehilangan.show');
    Route::get('/superadmin/surat/kehilangan/{id}/edit', [KehilanganController::class, 'edit'])
        ->name('superadmin.surat.kehilangan.edit');
    Route::put('/superadmin/surat/kehilangan/{id}', [KehilanganController::class, 'update'])
        ->name('superadmin.surat.kehilangan.update');
    Route::delete('/superadmin/surat/kehilangan/{id}', [KehilanganController::class, 'destroy'])
        ->name('superadmin.surat.kehilangan.delete');
    // Add new PDF export route
    Route::get('/superadmin/surat/kehilangan/{id}/pdf', [KehilanganController::class, 'generatePDF'])
        ->name('superadmin.surat.kehilangan.pdf');

    // SKCK routes
    Route::get('/superadmin/surat/skck/index', [SKCKController::class, 'index'])
        ->name('superadmin.surat.skck.index');
    Route::get('/superadmin/surat/skck/create', [SKCKController::class, 'create'])
        ->name('superadmin.surat.skck.create');
    Route::post('/superadmin/surat/skck', [SKCKController::class, 'store'])
        ->name('superadmin.surat.skck.store');
    Route::get('/superadmin/surat/skck/{id}/detail', [SKCKController::class, 'show'])
        ->name('superadmin.surat.skck.show');
    Route::get('/superadmin/surat/skck/{id}/edit', [SKCKController::class, 'edit'])
        ->name('superadmin.surat.skck.edit');
    Route::put('/superadmin/surat/skck/{id}', [SKCKController::class, 'update'])
        ->name('superadmin.surat.skck.update');
    Route::delete('/superadmin/surat/skck/{id}', [SKCKController::class, 'destroy'])
        ->name('superadmin.surat.skck.delete');
    Route::get('/superadmin/surat/skck/{id}/pdf', [SKCKController::class, 'generatePDF'])
        ->name('superadmin.surat.skck.pdf');

    // Domisili routes
    Route::get('/superadmin/surat/domisili/index', [DomisiliController::class, 'index'])
        ->name('superadmin.surat.domisili.index');
    Route::get('/superadmin/surat/domisili/create', [DomisiliController::class, 'create'])
        ->name('superadmin.surat.domisili.create');
    Route::post('/superadmin/surat/domisili', [DomisiliController::class, 'store'])
        ->name('superadmin.surat.domisili.store');
    Route::get('/superadmin/surat/domisili/{id}/detail', [DomisiliController::class, 'show'])
        ->name('superadmin.surat.domisili.show');
    Route::get('/superadmin/surat/domisili/{id}/edit', [DomisiliController::class, 'edit'])
        ->name('superadmin.surat.domisili.edit');
    Route::put('/superadmin/surat/domisili/{id}', [DomisiliController::class, 'update'])
        ->name('superadmin.surat.domisili.update');
    Route::delete('/superadmin/surat/domisili/{id}', [DomisiliController::class, 'destroy'])
        ->name('superadmin.surat.domisili.delete');
    // Add new PDF export route for domisili
    Route::get('/superadmin/surat/domisili/{id}/pdf', [DomisiliController::class, 'generatePDF'])
        ->name('superadmin.surat.domisili.pdf');

    // Domisili Usaha routes
    Route::get('/superadmin/surat/domisili-usaha', [DomisiliUsahaController::class, 'index'])
        ->name('superadmin.surat.domisili-usaha.index');
    Route::get('/superadmin/surat/domisili-usaha/create', [DomisiliUsahaController::class, 'create'])
        ->name('superadmin.surat.domisili-usaha.create');
    Route::post('/superadmin/surat/domisili-usaha', [DomisiliUsahaController::class, 'store'])
        ->name('superadmin.surat.domisili-usaha.store');
    Route::get('/superadmin/surat/domisili-usaha/{id}/detail', [DomisiliUsahaController::class, 'show'])
        ->name('superadmin.surat.domisili-usaha.show');
    Route::get('/superadmin/surat/domisili-usaha/{id}/edit', [DomisiliUsahaController::class, 'edit'])
        ->name('superadmin.surat.domisili-usaha.edit');
    Route::put('/superadmin/surat/domisili-usaha/{id}', [DomisiliUsahaController::class, 'update'])
        ->name('superadmin.surat.domisili-usaha.update');
    Route::delete('/superadmin/surat/domisili-usaha/{id}', [DomisiliUsahaController::class, 'destroy'])
        ->name('superadmin.surat.domisili-usaha.delete');
    // Add new PDF export route for domisili-usaha
    Route::get('/superadmin/surat/domisili-usaha/{id}/pdf', [DomisiliUsahaController::class, 'generatePDF'])
        ->name('superadmin.surat.domisili-usaha.pdf');

    // Ahli Waris routes
    Route::get('/superadmin/surat/ahli-waris', [AhliWarisController::class, 'index'])
        ->name('superadmin.surat.ahli-waris.index');
    Route::get('/superadmin/surat/ahli-waris/create', [AhliWarisController::class, 'create'])
        ->name('superadmin.surat.ahli-waris.create');
    Route::post('/superadmin/surat/ahli-waris', [AhliWarisController::class, 'store'])
        ->name('superadmin.surat.ahli-waris.store');
    Route::get('/superadmin/surat/ahli-waris/{id}/detail', [AhliWarisController::class, 'show'])
        ->name('superadmin.surat.ahli-waris.show');
    Route::get('/superadmin/surat/ahli-waris/{id}/edit', [AhliWarisController::class, 'edit'])
        ->name('superadmin.surat.ahli-waris.edit');
    Route::put('/superadmin/surat/ahli-waris/{id}', [AhliWarisController::class, 'update'])
        ->name('superadmin.surat.ahli-waris.update');
    Route::delete('/superadmin/surat/ahli-waris/{id}', [AhliWarisController::class, 'destroy'])
        ->name('superadmin.surat.ahli-waris.delete');
    // Add PDF route
    Route::get('/superadmin/surat/ahli-waris/{id}/pdf', [AhliWarisController::class, 'generatePDF'])
        ->name('superadmin.surat.ahli-waris.pdf');

    // Birth Certificate (Kelahiran) routes
    Route::get('/superadmin/surat/kelahiran', [KelahiranController::class, 'index'])
        ->name('superadmin.surat.kelahiran.index');
    Route::get('/superadmin/surat/kelahiran/create', [KelahiranController::class, 'create'])
        ->name('superadmin.surat.kelahiran.create');
    Route::post('/superadmin/surat/kelahiran', [KelahiranController::class, 'store'])
        ->name('superadmin.surat.kelahiran.store');
    Route::get('/superadmin/surat/kelahiran/{id}/detail', [KelahiranController::class, 'show'])
        ->name('superadmin.surat.kelahiran.show');
    Route::get('/superadmin/surat/kelahiran/{id}/edit', [KelahiranController::class, 'edit'])
        ->name('superadmin.surat.kelahiran.edit');
    Route::put('/superadmin/surat/kelahiran/{id}', [KelahiranController::class, 'update'])
        ->name('superadmin.surat.kelahiran.update');
    Route::delete('/superadmin/surat/kelahiran/{id}', [KelahiranController::class, 'destroy'])
        ->name('superadmin.surat.kelahiran.delete');
    Route::get('/superadmin/surat/kelahiran/{id}/pdf', [KelahiranController::class, 'exportPDF'])
        ->name('superadmin.surat.kelahiran.pdf');

    // Death Certificate (Kematian) routes
    Route::get('/superadmin/surat/kematian', [KematianController::class, 'index'])
        ->name('superadmin.surat.kematian.index');
    Route::get('/superadmin/surat/kematian/create', [KematianController::class, 'create'])
        ->name('superadmin.surat.kematian.create');
    Route::post('/superadmin/surat/kematian', [KematianController::class, 'store'])
        ->name('superadmin.surat.kematian.store');
    Route::get('/superadmin/surat/kematian/{id}/detail', [KematianController::class, 'show'])
        ->name('superadmin.surat.kematian.show');
    Route::get('/superadmin/surat/kematian/{id}/edit', [KematianController::class, 'edit'])
        ->name('superadmin.surat.kematian.edit');
    Route::put('/superadmin/surat/kematian/{id}', [KematianController::class, 'update'])
        ->name('superadmin.surat.kematian.update');
    Route::delete('/superadmin/surat/kematian/{id}', [KematianController::class, 'destroy'])
        ->name('superadmin.surat.kematian.delete');
    Route::get('/superadmin/surat/kematian/{id}/pdf', [KematianController::class, 'exportPDF'])->name('superadmin.surat.kematian.pdf');

    // Entertainment Permit (Izin Keramaian) routes
    Route::get('/superadmin/surat/keramaian', [IzinKeramaianController::class, 'index'])
        ->name('superadmin.surat.keramaian.index');
    Route::get('/superadmin/surat/keramaian/create', [IzinKeramaianController::class, 'create'])
        ->name('superadmin.surat.keramaian.create');
    Route::post('/superadmin/surat/keramaian', [IzinKeramaianController::class, 'store'])
        ->name('superadmin.surat.keramaian.store');
    Route::get('/superadmin/surat/keramaian/{id}/detail', [IzinKeramaianController::class, 'show'])
        ->name('superadmin.surat.keramaian.show');
    Route::get('/superadmin/surat/keramaian/{id}/edit', [IzinKeramaianController::class, 'edit'])
        ->name('superadmin.surat.keramaian.edit');
    Route::put('/superadmin/surat/keramaian/{id}', [IzinKeramaianController::class, 'update'])
        ->name('superadmin.surat.keramaian.update');
    Route::delete('/superadmin/surat/keramaian/{id}', [IzinKeramaianController::class, 'destroy'])
        ->name('superadmin.surat.keramaian.delete');
    // Add new route for PDF export
    Route::get('/superadmin/surat/keramaian/{id}/pdf', [IzinKeramaianController::class, 'exportPDF'])
        ->name('superadmin.surat.keramaian.pdf');

    // Rental House Permit (Izin Rumah Sewa) routes
    Route::get('/superadmin/surat/rumah-sewa', [RumahSewaController::class, 'index'])
        ->name('superadmin.surat.rumah-sewa.index');
    Route::get('/superadmin/surat/rumah-sewa/create', [RumahSewaController::class, 'create'])
        ->name('superadmin.surat.rumah-sewa.create');
    Route::post('/superadmin/surat/rumah-sewa', [RumahSewaController::class, 'store'])
        ->name('superadmin.surat.rumah-sewa.store');
    Route::get('/superadmin/surat/rumah-sewa/{id}/detail', [RumahSewaController::class, 'show'])
        ->name('superadmin.surat.rumah-sewa.show');
    Route::get('/superadmin/surat/rumah-sewa/{id}/edit', [RumahSewaController::class, 'edit'])
        ->name('superadmin.surat.rumah-sewa.edit');
    Route::put('/superadmin/surat/rumah-sewa/{id}', [RumahSewaController::class, 'update'])
        ->name('superadmin.surat.rumah-sewa.update');
    Route::delete('/superadmin/surat/rumah-sewa/{id}', [RumahSewaController::class, 'destroy'])
        ->name('superadmin.surat.rumah-sewa.delete');
    // Add new route for PDF export
    Route::get('/superadmin/surat/rumah-sewa/{id}/pdf', [RumahSewaController::class, 'exportPDF'])
        ->name('superadmin.surat.rumah-sewa.pdf');

    // Pengantar KTP routes
    Route::get('/superadmin/surat/pengantar-ktp', [PengantarKtpController::class, 'index'])
        ->name('superadmin.surat.pengantar-ktp.index');
    Route::get('/superadmin/surat/pengantar-ktp/create', [PengantarKtpController::class, 'create'])
        ->name('superadmin.surat.pengantar-ktp.create');
    Route::post('/superadmin/surat/pengantar-ktp', [PengantarKtpController::class, 'store'])
        ->name('superadmin.surat.pengantar-ktp.store');
    Route::get('/superadmin/surat/pengantar-ktp/{id}/detail', [PengantarKtpController::class, 'show'])
        ->name('superadmin.surat.pengantar-ktp.show');
    Route::get('/superadmin/surat/pengantar-ktp/{id}/edit', [PengantarKtpController::class, 'edit'])
        ->name('superadmin.surat.pengantar-ktp.edit');
    Route::put('/superadmin/surat/pengantar-ktp/{id}', [PengantarKtpController::class, 'update'])
        ->name('superadmin.surat.pengantar-ktp.update');
    Route::delete('/superadmin/surat/pengantar-ktp/{id}', [PengantarKtpController::class, 'destroy'])
        ->name('superadmin.surat.pengantar-ktp.delete');
    Route::get('/surat/pengantar-ktp/{id}/pdf', [PengantarKtpController::class, 'exportPDF'])->name('superadmin.surat.pengantar-ktp.pdf');
    Route::get('/superadmin/surat/pengantar-ktp/{id}/pdf', [PengantarKtpController::class, 'exportPDF'])->name('superadmin.surat.pengantar-ktp.pdf');

    // Routes for Excel import
    Route::post('/superadmin/biodata/import', [BiodataController::class, 'import'])->name('superadmin.biodata.import');
    Route::get('/superadmin/biodata/template', [BiodataController::class, 'downloadTemplate'])->name('superadmin.biodata.template');

    // Penandatangan routes
    Route::get('/superadmin/datamaster/surat/penandatangan', [PenandatangananController::class, 'index'])
        ->name('superadmin.datamaster.surat.penandatangan.index');
    Route::get('/superadmin/datamaster/surat/penandatangan/create', [PenandatangananController::class, 'create'])
        ->name('superadmin.datamaster.surat.penandatangan.create');
    Route::post('/superadmin/datamaster/surat/penandatangan', [PenandatangananController::class, 'store'])
        ->name('superadmin.datamaster.surat.penandatangan.store');
    Route::get('/superadmin/datamaster/surat/penandatangan/{id}/edit', [PenandatangananController::class, 'edit'])
        ->name('superadmin.datamaster.surat.penandatangan.edit');
    Route::put('/superadmin/datamaster/surat/penandatangan/{id}', [PenandatangananController::class, 'update'])
        ->name('superadmin.datamaster.surat.penandatangan.update');
    Route::delete('/superadmin/datamaster/surat/penandatangan/{id}', [PenandatangananController::class, 'destroy'])
        ->name('superadmin.datamaster.surat.penandatangan.destroy');
    Route::get('/penandatangan/dropdown', [PenandatangananController::class, 'getForDropdown'])->name('penandatangan.dropdown');

    // Keperluan routes
    Route::get('/superadmin/datamaster/masterkeperluan/keperluan', [KeperluanController::class, 'index'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.index');
    Route::get('/superadmin/datamaster/masterkeperluan/keperluan/create', [KeperluanController::class, 'create'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.create');
    Route::post('/superadmin/datamaster/masterkeperluan/keperluan', [KeperluanController::class, 'store'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.store');
    Route::get('/superadmin/datamaster/masterkeperluan/keperluan/{keperluan}/edit', [KeperluanController::class, 'edit'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.edit');
    Route::put('/superadmin/datamaster/masterkeperluan/keperluan/{keperluan}', [KeperluanController::class, 'update'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.update');
    Route::delete('/superadmin/datamaster/masterkeperluan/keperluan/{keperluan}', [KeperluanController::class, 'destroy'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.destroy');

    //Route Kelola Aset
    // Klasifikasi routes
    Route::get('/superadmin/datamaster/klasifikasi', [KlasifikasiController::class, 'index'])
        ->name('superadmin.datamaster.klasifikasi.index');
    Route::get('/superadmin/datamaster/klasifikasi/create', [KlasifikasiController::class, 'create'])
        ->name('superadmin.datamaster.klasifikasi.create');
    Route::post('/superadmin/datamaster/klasifikasi', [KlasifikasiController::class, 'store'])
        ->name('superadmin.datamaster.klasifikasi.store');
    Route::get('/superadmin/datamaster/klasifikasi/{id}/edit', [KlasifikasiController::class, 'edit'])
        ->name('superadmin.datamaster.klasifikasi.edit');
    Route::put('/superadmin/datamaster/klasifikasi/{id}', [KlasifikasiController::class, 'update'])
        ->name('superadmin.datamaster.klasifikasi.update');
    Route::delete('/superadmin/datamaster/klasifikasi/{id}', [KlasifikasiController::class, 'destroy'])
        ->name('superadmin.datamaster.klasifikasi.destroy');

    // Jenis Aset routes
    Route::get('/superadmin/datamaster/jenis-aset', [JenisAsetController::class, 'index'])
        ->name('superadmin.datamaster.jenis-aset.index');
    Route::get('/superadmin/datamaster/jenis-aset/create', [JenisAsetController::class, 'create'])
        ->name('superadmin.datamaster.jenis-aset.create');
    Route::post('/superadmin/datamaster/jenis-aset', [JenisAsetController::class, 'store'])
        ->name('superadmin.datamaster.jenis-aset.store');
    Route::get('/superadmin/datamaster/jenis-aset/{id}/edit', [JenisAsetController::class, 'edit'])
        ->name('superadmin.datamaster.jenis-aset.edit');
    Route::put('/superadmin/datamaster/jenis-aset/{id}', [JenisAsetController::class, 'update'])
        ->name('superadmin.datamaster.jenis-aset.update');
    Route::delete('/superadmin/datamaster/jenis-aset/{id}', [JenisAsetController::class, 'destroy'])
        ->name('superadmin.datamaster.jenis-aset.destroy');

    Route::get('/superadmin/datamaster/lapordesa', [LaporDesaController::class, 'index'])
        ->name('superadmin.datamaster.lapordesa.index');
    Route::get('/superadmin/datamaster/lapordesa/create', [LaporDesaController::class, 'create'])
        ->name('superadmin.datamaster.lapordesa.create');
    Route::post('/superadmin/datamaster/lapordesa', [LaporDesaController::class, 'store'])
        ->name('superadmin.datamaster.lapordesa.store');
    Route::get('/superadmin/datamaster/lapordesa/{id}/edit', [LaporDesaController::class, 'edit'])
        ->name('superadmin.datamaster.lapordesa.edit');
    Route::put('/superadmin/datamaster/lapordesa/{id}', [LaporDesaController::class, 'update'])
        ->name('superadmin.datamaster.lapordesa.update');
    Route::delete('/superadmin/datamaster/lapordesa/{id}', [LaporDesaController::class, 'destroy'])
        ->name('superadmin.datamaster.lapordesa.destroy');

    //Berita Desa untuk Superadmin
    Route::get('/superadmin/berita-desa', [SuperadminBeritaDesaController::class, 'index'])
        ->name('superadmin.berita-desa.index');
    Route::get('/superadmin/berita-desa/create', [SuperadminBeritaDesaController::class, 'create'])
        ->name('superadmin.berita-desa.create');
    Route::post('/superadmin/berita-desa', [SuperadminBeritaDesaController::class, 'store'])
        ->name('superadmin.berita-desa.store');
    Route::get('/superadmin/berita-desa/{id}', [SuperadminBeritaDesaController::class, 'show'])
        ->name('superadmin.berita-desa.show');
    Route::get('/superadmin/berita-desa/{id}/edit', [SuperadminBeritaDesaController::class, 'edit'])
        ->name('superadmin.berita-desa.edit');
    Route::put('/superadmin/berita-desa/{id}', [SuperadminBeritaDesaController::class, 'update'])
        ->name('superadmin.berita-desa.update');
    Route::delete('/superadmin/berita-desa/{id}', [SuperadminBeritaDesaController::class, 'destroy'])
        ->name('superadmin.berita-desa.destroy');


});

// Route untuk admin kabupaten - menggunakan web guard
Route::middleware(['auth:web', 'role:admin kabupaten'])->group(function () {
    Route::get('/admin/kabupaten/index', [DashboardController::class, 'indexKabupaten'])
        ->name('admin.kabupaten.index');

    // Admin Kabupaten Profile routes - Ensure these are correctly defined
    Route::get('/admin/kabupaten/profile', [ProfileKabController::class, 'index'])
        ->name('admin.kabupaten.profile.index');
    Route::get('/admin/kabupaten/profile/edit', [ProfileKabController::class, 'edit'])
        ->name('admin.kabupaten.profile.edit');
    Route::put('/admin/kabupaten/profile/update', [ProfileKabController::class, 'update'])
        ->name('admin.kabupaten.profile.update');
    Route::post('/admin/kabupaten/profile/update-photo', [ProfileKabController::class, 'updatePhoto'])
        ->name('admin.kabupaten.profile.update-photo');
});

// Route untuk admin desa - menggunakan web guard
Route::middleware(['auth:web', 'role:admin desa'])->group(function () {
    Route::get('/admin/desa/index', [DashboardController::class, 'indexDesa'])
        ->name('admin.desa.index');

    // Admin Desa Profile routes
    Route::get('/admin/desa/profile', [ProfileDesaController::class, 'index'])
        ->name('admin.desa.profile.index');
    Route::get('/admin/desa/profile/edit', [ProfileDesaController::class, 'edit'])
        ->name('admin.desa.profile.edit');
    Route::put('/admin/desa/profile/update', [ProfileDesaController::class, 'update'])
        ->name('admin.desa.profile.update');
    Route::post('/admin/desa/profile/update-photo', [ProfileDesaController::class, 'updatePhoto'])
        ->name('admin.desa.profile.update-photo');

    // Routes for managing data related to citizens
    Route::get('/admin/desa/biodata/index', [BiodataController::class, 'index'])
        ->name('admin.desa.biodata.index');
    Route::get('/admin/desa/biodata/create', [BiodataController::class, 'create'])
        ->name('admin.desa.biodata.create');
    Route::post('/admin/desa/biodata/store', [BiodataController::class, 'store'])
        ->name('admin.desa.biodata.store');
    Route::get('/admin/desa/biodata/{nik}/edit', [BiodataController::class, 'edit'])
        ->name('admin.desa.biodata.edit');
    Route::put('/admin/desa/biodata/{nik}', [BiodataController::class, 'update'])
        ->name('admin.desa.biodata.update');
    Route::delete('/admin/desa/biodata/{id}', [BiodataController::class, 'destroy'])
        ->name('admin.desa.biodata.destroy');

    // Biodata change approvals
    Route::get('/admin/desa/biodata-approval', [AdminDesaBiodataApprovalController::class, 'index'])
        ->name('admin.desa.biodata-approval.index');
    Route::get('/admin/desa/biodata-approval/{id}', [AdminDesaBiodataApprovalController::class, 'show'])
        ->name('admin.desa.biodata-approval.show');
    Route::post('/admin/desa/biodata-approval/{id}/approve', [AdminDesaBiodataApprovalController::class, 'approve'])
        ->name('admin.desa.biodata-approval.approve');
    Route::post('/admin/desa/biodata-approval/{id}/reject', [AdminDesaBiodataApprovalController::class, 'reject'])
        ->name('admin.desa.biodata-approval.reject');

    // Routes for managing family data
    Route::get('/admin/desa/datakk/index', [DataKKController::class, 'index'])
        ->name('admin.desa.datakk.index');
    Route::get('/admin/desa/datakk/create', [DataKKController::class, 'create'])
        ->name('admin.desa.datakk.create');
    Route::post('/admin/desa/datakk/store', [DataKKController::class, 'store'])
        ->name('admin.desa.datakk.store');
    Route::get('/admin/desa/datakk/{id}/edit', [DataKKController::class, 'edit'])
        ->name('admin.desa.datakk.edit');
    Route::put('/admin/desa/datakk/{id}', [DataKKController::class, 'update'])
        ->name('admin.desa.datakk.update');
    Route::delete('/admin/desa/datakk/{id}', [DataKKController::class, 'destroy'])
        ->name('admin.desa.datakk.destroy');

    // Routes for Excel import
    Route::get('/admin/desa/biodata/export', [BiodataController::class, 'export'])
        ->name('admin.desa.biodata.export');


    // Routes for reporting
    Route::get('/admin/desa/datamaster/lapordesa', [LaporDesaController::class, 'index'])
        ->name('admin.desa.datamaster.lapordesa.index');
    Route::get('/admin/desa/datamaster/lapordesa/create', [LaporDesaController::class, 'create'])
        ->name('admin.desa.datamaster.lapordesa.create');
    Route::post('/admin/desa/datamaster/lapordesa', [LaporDesaController::class, 'store'])
        ->name('admin.desa.datamaster.lapordesa.store');
    Route::get('/admin/desa/datamaster/lapordesa/{id}/edit', [LaporDesaController::class, 'edit'])
        ->name('admin.desa.datamaster.lapordesa.edit');
    Route::put('/admin/desa/datamaster/lapordesa/{id}', [LaporDesaController::class, 'update'])
        ->name('admin.desa.datamaster.lapordesa.update');
    Route::delete('/admin/desa/datamaster/lapordesa/{id}', [LaporDesaController::class, 'destroy'])
        ->name('admin.desa.datamaster.lapordesa.destroy');

    // Laporan Desa Routes
    Route::get('/admin/desa/laporan-desa', [AdminDesaLaporanDesaController::class, 'index'])
        ->name(name: 'admin.desa.laporan-desa.index');
    Route::get('/admin/desa/laporan-desa/{id}', [AdminDesaLaporanDesaController::class, 'show'])
        ->name('admin.desa.laporan-desa.show');
    Route::put('/admin/desa/laporan-desa/{id}/status', [AdminDesaLaporanDesaController::class, 'updateStatus'])
        ->name('admin.desa.laporan-desa.update-status');

    // Di dalam grup middleware admin desa
    Route::get('/admin/desa/datakk/export', [DataKKController::class, 'export'])
        ->name('admin.desa.datakk.export');

    // Administrasi routes
    Route::get('/admin/desa/surat/administrasi/index', [AdministrasiController::class, 'index'])
        ->name('admin.desa.surat.administrasi.index');
    Route::get('/admin/desa/surat/administrasi/create', [AdministrasiController::class, 'create'])
        ->name('admin.desa.surat.administrasi.create');
    Route::post('/admin/desa/surat/administrasi', [AdministrasiController::class, 'store'])
        ->name('admin.desa.surat.administrasi.store');
    Route::get('/admin/desa/surat/administrasi/{id}/detail', [AdministrasiController::class, 'show'])
        ->name('admin.desa.surat.administrasi.show');
    Route::get('/admin/desa/surat/administrasi/{id}/edit', [AdministrasiController::class, 'edit'])
        ->name('admin.desa.surat.administrasi.edit');
    Route::put('/admin/desa/surat/administrasi/{id}', [AdministrasiController::class, 'update'])
        ->name('admin.desa.surat.administrasi.update');
    Route::delete('/admin/desa/surat/administrasi/{id}', [AdministrasiController::class, 'destroy'])
        ->name('admin.desa.surat.administrasi.delete');
    Route::get('/admin/desa/surat/administrasi/{id}/pdf', [AdministrasiController::class, 'generatePDF'])
        ->name('admin.desa.surat.administrasi.pdf');

    // Kehilangan routes
    Route::get('/admin/desa/surat/kehilangan/index', [KehilanganController::class, 'index'])
        ->name('admin.desa.surat.kehilangan.index');
    Route::get('/admin/desa/surat/kehilangan/create', [KehilanganController::class, 'create'])
        ->name('admin.desa.surat.kehilangan.create');
    Route::post('/admin/desa/surat/kehilangan', [KehilanganController::class, 'store'])
        ->name('admin.desa.surat.kehilangan.store');
    Route::get('/admin/desa/surat/kehilangan/{id}/detail', [KehilanganController::class, 'show'])
        ->name('admin.desa.surat.kehilangan.show');
    Route::get('/admin/desa/surat/kehilangan/{id}/edit', [KehilanganController::class, 'edit'])
        ->name('admin.desa.surat.kehilangan.edit');
    Route::put('/admin/desa/surat/kehilangan/{id}', [KehilanganController::class, 'update'])
        ->name('admin.desa.surat.kehilangan.update');
    Route::delete('/admin/desa/surat/kehilangan/{id}', [KehilanganController::class, 'destroy'])
        ->name('admin.desa.surat.kehilangan.delete');
    // Add new PDF export route
    Route::get('/admin/desa/surat/kehilangan/{id}/pdf', [KehilanganController::class, 'generatePDF'])
        ->name('admin.desa.surat.kehilangan.pdf');

    // SKCK routes
    Route::get('/admin/desa/surat/skck/index', [SKCKController::class, 'index'])
        ->name('admin.desa.surat.skck.index');
    Route::get('/admin/desa/surat/skck/create', [SKCKController::class, 'create'])
        ->name('admin.desa.surat.skck.create');
    Route::post('/admin/desa/surat/skck', [SKCKController::class, 'store'])
        ->name('admin.desa.surat.skck.store');
    Route::get('/admin/desa/surat/skck/{id}/detail', [SKCKController::class, 'show'])
        ->name('admin.desa.surat.skck.show');
    Route::get('/admin/desa/surat/skck/{id}/edit', [SKCKController::class, 'edit'])
        ->name('admin.desa.surat.skck.edit');
    Route::put('/admin/desa/surat/skck/{id}', [SKCKController::class, 'update'])
        ->name('admin.desa.surat.skck.update');
    Route::delete('/admin/desa/surat/skck/{id}', [SKCKController::class, 'destroy'])
        ->name('admin.desa.surat.skck.delete');
    Route::get('/admin/desa/surat/skck/{id}/pdf', [SKCKController::class, 'generatePDF'])
        ->name('admin.desa.surat.skck.pdf');

    // Domisili routes
    Route::get('/admin/desa/surat/domisili/index', [DomisiliController::class, 'index'])
        ->name('admin.desa.surat.domisili.index');
    Route::get('/admin/desa/surat/domisili/create', [DomisiliController::class, 'create'])
        ->name('admin.desa.surat.domisili.create');
    Route::post('/admin/desa/surat/domisili', [DomisiliController::class, 'store'])
        ->name('admin.desa.surat.domisili.store');
    Route::get('/admin/desa/surat/domisili/{id}/detail', [DomisiliController::class, 'show'])
        ->name('admin.desa.surat.domisili.show');
    Route::get('/admin/desa/surat/domisili/{id}/edit', [DomisiliController::class, 'edit'])
        ->name('admin.desa.surat.domisili.edit');
    Route::put('/admin/desa/surat/domisili/{id}', [DomisiliController::class, 'update'])
        ->name('admin.desa.surat.domisili.update');
    Route::delete('/admin/desa/surat/domisili/{id}', [DomisiliController::class, 'destroy'])
        ->name('admin.desa.surat.domisili.delete');
    // Add new PDF export route for domisili
    Route::get('/admin/desa/surat/domisili/{id}/pdf', [DomisiliController::class, 'generatePDF'])
        ->name('admin.desa.surat.domisili.pdf');

    // Domisili Usaha routes
    Route::get('/admin/desa/surat/domisili-usaha/index', [DomisiliUsahaController::class, 'index'])
        ->name('admin.desa.surat.domisili-usaha.index');
    Route::get('/admin/desa/surat/domisili-usaha/create', [DomisiliUsahaController::class, 'create'])
        ->name('admin.desa.surat.domisili-usaha.create');
    Route::post('/admin/desa/surat/domisili-usaha', [DomisiliUsahaController::class, 'store'])
        ->name('admin.desa.surat.domisili-usaha.store');
    Route::get('/admin/desa/surat/domisili-usaha/{id}/detail', [DomisiliUsahaController::class, 'show'])
        ->name('admin.desa.surat.domisili-usaha.show');
    Route::get('/admin/desa/surat/domisili-usaha/{id}/edit', [DomisiliUsahaController::class, 'edit'])
        ->name('admin.desa.surat.domisili-usaha.edit');
    Route::put('/admin/desa/surat/domisili-usaha/{id}', [DomisiliUsahaController::class, 'update'])
        ->name('admin.desa.surat.domisili-usaha.update');
    Route::delete('/admin/desa/surat/domisili-usaha/{id}', [DomisiliUsahaController::class, 'destroy'])
        ->name('admin.desa.surat.domisili-usaha.delete');
    // Add new PDF export route for domisili-usaha
    Route::get('/admin/desa/surat/domisili-usaha/{id}/pdf', [DomisiliUsahaController::class, 'generatePDF'])
        ->name('admin.desa.surat.domisili-usaha.pdf');

    // Ahli Waris routes
    Route::get('/admin/desa/surat/ahli-waris/index', [AhliWarisController::class, 'index'])
        ->name('admin.desa.surat.ahli-waris.index');
    Route::get('/admin/desa/surat/ahli-waris/create', [AhliWarisController::class, 'create'])
        ->name('admin.desa.surat.ahli-waris.create');
    Route::post('/admin/desa/surat/ahli-waris', [AhliWarisController::class, 'store'])
        ->name('admin.desa.surat.ahli-waris.store');
    Route::get('/admin/desa/surat/ahli-waris/{id}/detail', [AhliWarisController::class, 'show'])
        ->name('admin.desa.surat.ahli-waris.show');
    Route::get('/admin/desa/surat/ahli-waris/{id}/edit', [AhliWarisController::class, 'edit'])
        ->name('admin.desa.surat.ahli-waris.edit');
    Route::put('/admin/desa/surat/ahli-waris/{id}', [AhliWarisController::class, 'update'])
        ->name('admin.desa.surat.ahli-waris.update');
    Route::delete('/admin/desa/surat/ahli-waris/{id}', [AhliWarisController::class, 'destroy'])
        ->name('admin.desa.surat.ahli-waris.delete');
    // Add PDF route
    Route::get('/admin/desa/surat/ahli-waris/{id}/pdf', [AhliWarisController::class, 'generatePDF'])
        ->name('admin.desa.surat.ahli-waris.pdf');

    // Birth Certificate (Kelahiran) routes
    Route::get('/admin/desa/surat/kelahiran/index', [KelahiranController::class, 'index'])
        ->name('admin.desa.surat.kelahiran.index');
    Route::get('/admin/desa/surat/kelahiran/create', [KelahiranController::class, 'create'])
        ->name('admin.desa.surat.kelahiran.create');
    Route::post('/admin/desa/surat/kelahiran', [KelahiranController::class, 'store'])
        ->name('admin.desa.surat.kelahiran.store');
    Route::get('/admin/desa/surat/kelahiran/{id}/detail', [KelahiranController::class, 'show'])
        ->name('admin.desa.surat.kelahiran.show');
    Route::get('/admin/desa/surat/kelahiran/{id}/edit', [KelahiranController::class, 'edit'])
        ->name('admin.desa.surat.kelahiran.edit');
    Route::put('/admin/desa/surat/kelahiran/{id}', [KelahiranController::class, 'update'])
        ->name('admin.desa.surat.kelahiran.update');
    Route::delete('/admin/desa/surat/kelahiran/{id}', [KelahiranController::class, 'destroy'])
        ->name('admin.desa.surat.kelahiran.delete');
    Route::get('/admin/desa/surat/kelahiran/{id}/pdf', [KelahiranController::class, 'exportPDF'])
        ->name('admin.desa.surat.kelahiran.pdf');

    // Death Certificate (Kematian) routes
    Route::get('/admin/desa/surat/kematian/index', [KematianController::class, 'index'])
        ->name('admin.desa.surat.kematian.index');
    Route::get('/admin/desa/surat/kematian/create', [KematianController::class, 'create'])
        ->name('admin.desa.surat.kematian.create');
    Route::post('/admin/desa/surat/kematian', [KematianController::class, 'store'])
        ->name('admin.desa.surat.kematian.store');
    Route::get('/admin/desa/surat/kematian/{id}/detail', [KematianController::class, 'show'])
        ->name('admin.desa.surat.kematian.show');
    Route::get('/admin/desa/surat/kematian/{id}/edit', [KematianController::class, 'edit'])
        ->name('admin.desa.surat.kematian.edit');
    Route::put('/admin/desa/surat/kematian/{id}', [KematianController::class, 'update'])
        ->name('admin.desa.surat.kematian.update');
    Route::delete('/admin/desa/surat/kematian/{id}', [KematianController::class, 'destroy'])
        ->name('admin.desa.surat.kematian.delete');
    Route::get('/admin/desa/surat/kematian/{id}/pdf', [KematianController::class, 'exportPDF'])
        ->name('admin.desa.surat.kematian.pdf');

    // Entertainment Permit (Izin Keramaian) routes
    Route::get('/admin/desa/surat/keramaian/index', [IzinKeramaianController::class, 'index'])
        ->name('admin.desa.surat.keramaian.index');
    Route::get('/admin/desa/surat/keramaian/create', [IzinKeramaianController::class, 'create'])
        ->name('admin.desa.surat.keramaian.create');
    Route::post('/admin/desa/surat/keramaian', [IzinKeramaianController::class, 'store'])
        ->name('admin.desa.surat.keramaian.store');
    Route::get('/admin/desa/surat/keramaian/{id}/detail', [IzinKeramaianController::class, 'show'])
        ->name('admin.desa.surat.keramaian.show');
    Route::get('/admin/desa/surat/keramaian/{id}/edit', [IzinKeramaianController::class, 'edit'])
        ->name('admin.desa.surat.keramaian.edit');
    Route::put('/admin/desa/surat/keramaian/{id}', [IzinKeramaianController::class, 'update'])
        ->name('admin.desa.surat.keramaian.update');
    Route::delete('/admin/desa/surat/keramaian/{id}', [IzinKeramaianController::class, 'destroy'])
        ->name('admin.desa.surat.keramaian.delete');
    // Add new route for PDF export
    Route::get('/admin/desa/surat/keramaian/{id}/pdf', [IzinKeramaianController::class, 'exportPDF'])
        ->name('admin.desa.surat.keramaian.pdf');

    // Rental House Permit (Izin Rumah Sewa) routes
    Route::get('/admin/desa/surat/rumah-sewa/index', [RumahSewaController::class, 'index'])
        ->name('admin.desa.surat.rumah-sewa.index');
    Route::get('/admin/desa/surat/rumah-sewa/create', [RumahSewaController::class, 'create'])
        ->name('admin.desa.surat.rumah-sewa.create');
    Route::post('/admin/desa/surat/rumah-sewa', [RumahSewaController::class, 'store'])
        ->name('admin.desa.surat.rumah-sewa.store');
    Route::get('/admin/desa/surat/rumah-sewa/{id}/detail', [RumahSewaController::class, 'show'])
        ->name('admin.desa.surat.rumah-sewa.show');
    Route::get('/admin/desa/surat/rumah-sewa/{id}/edit', [RumahSewaController::class, 'edit'])
        ->name('admin.desa.surat.rumah-sewa.edit');
    Route::put('/admin/desa/surat/rumah-sewa/{id}', [RumahSewaController::class, 'update'])
        ->name('admin.desa.surat.rumah-sewa.update');
    Route::delete('/admin/desa/surat/rumah-sewa/{id}', [RumahSewaController::class, 'destroy'])
        ->name('admin.desa.surat.rumah-sewa.delete');
    // Add new route for PDF export
    Route::get('/admin/desa/surat/rumah-sewa/{id}/pdf', [RumahSewaController::class, 'exportPDF'])
        ->name('admin.desa.surat.rumah-sewa.pdf');

    // Pengantar KTP routes
    Route::get('/admin/desa/surat/pengantar-ktp/index', [PengantarKtpController::class, 'index'])
        ->name('admin.desa.surat.pengantar-ktp.index');
    Route::get('/admin/desa/surat/pengantar-ktp/create', [PengantarKtpController::class, 'create'])
        ->name('admin.desa.surat.pengantar-ktp.create');
    Route::post('/admin/desa/surat/pengantar-ktp', [PengantarKtpController::class, 'store'])
        ->name('admin.desa.surat.pengantar-ktp.store');
    Route::get('/admin/desa/surat/pengantar-ktp/{id}/detail', [PengantarKtpController::class, 'show'])
        ->name('admin.desa.surat.pengantar-ktp.show');
    Route::get('/admin/desa/surat/pengantar-ktp/{id}/edit', [PengantarKtpController::class, 'edit'])
        ->name('admin.desa.surat.pengantar-ktp.edit');
    Route::put('/admin/desa/surat/pengantar-ktp/{id}', [PengantarKtpController::class, 'update'])
        ->name('admin.desa.surat.pengantar-ktp.update');
    Route::delete('/admin/desa/surat/pengantar-ktp/{id}', [PengantarKtpController::class, 'destroy'])
        ->name('admin.desa.surat.pengantar-ktp.delete');
    Route::get('/admin/desa/surat/pengantar-ktp/{id}/pdf', [PengantarKtpController::class, 'exportPDF'])
        ->name('admin.desa.surat.pengantar-ktp.pdf');

    //Berita Desa (Admin Desa)
    Route::get('/admin/desa/berita-desa', [AdminDesaBeritaDesaController::class, 'index'])
        ->name('admin.desa.berita-desa.index');
    Route::get('/admin/desa/berita-desa/pending', [AdminDesaBeritaDesaController::class, 'pending'])
        ->name('admin.desa.berita-desa.pending');
    Route::get('/admin/desa/berita-desa/create', [AdminDesaBeritaDesaController::class, 'create'])
        ->name('admin.desa.berita-desa.create');
    Route::post('/admin/desa/berita-desa', [AdminDesaBeritaDesaController::class, 'store'])
        ->name('admin.desa.berita-desa.store');
    Route::get('/admin/desa/berita-desa/{id}/edit', [AdminDesaBeritaDesaController::class, 'edit'])
        ->name('admin.desa.berita-desa.edit');
    Route::put('/admin/desa/berita-desa/{id}', [AdminDesaBeritaDesaController::class, 'update'])
        ->name('admin.desa.berita-desa.update');
    Route::delete('/admin/desa/berita-desa/{id}', [AdminDesaBeritaDesaController::class, 'destroy'])
        ->name('admin.desa.berita-desa.destroy');
    Route::get('/admin/desa/berita-desa/{id}', [AdminDesaBeritaDesaController::class, 'show'])
        ->name('admin.desa.berita-desa.show');
    // Pengumuman admin desa
    Route::get('/admin/desa/pengumuman', [AdminDesaPengumumanController::class, 'index'])
        ->name('admin.desa.pengumuman.index');
    Route::get('/admin/desa/pengumuman/create', [AdminDesaPengumumanController::class, 'create'])
        ->name('admin.desa.pengumuman.create');
    Route::post('/admin/desa/pengumuman', [AdminDesaPengumumanController::class, 'store'])
        ->name('admin.desa.pengumuman.store');
    Route::get('/admin/desa/pengumuman/{id}/edit', [AdminDesaPengumumanController::class, 'edit'])
        ->name('admin.desa.pengumuman.edit');
    Route::put('/admin/desa/pengumuman/{id}', [AdminDesaPengumumanController::class, 'update'])
        ->name('admin.desa.pengumuman.update');
    Route::delete('/admin/desa/pengumuman/{id}', [AdminDesaPengumumanController::class, 'destroy'])
        ->name('admin.desa.pengumuman.destroy');
    Route::get('/admin/desa/pengumuman/{id}', [AdminDesaPengumumanController::class, 'show'])
        ->name('admin.desa.pengumuman.show');
    Route::post('/admin/desa/berita-desa/{id}/approve', [AdminDesaBeritaDesaController::class, 'approve'])
        ->name('admin.desa.berita-desa.approve');
    Route::post('/admin/desa/berita-desa/{id}/reject', [AdminDesaBeritaDesaController::class, 'reject'])
        ->name('admin.desa.berita-desa.reject');
});

// Route untuk operator - menggunakan web guard
Route::middleware(['auth:web', 'role:operator'])->group(function () {
    Route::get('/operator/index', function () {
        return view('operator.index');
    });
});

// Route untuk penduduk - menggunakan auth penduduk
Route::middleware(['auth:penduduk'])->group(function () {
    Route::get('/user/index', function () {
        return view('user.index');
    });

    Route::get('/user/profile', [ProfileController::class, 'index'])
        ->name('user.profile.index');
    Route::get('/user/profile/edit', [ProfileController::class, 'edit'])
        ->name('user.profile.edit');
    Route::put('/user/profile', [ProfileController::class, 'update'])
        ->name('user.profile.update');
    Route::post('/user/profile/request-approval', [ProfileController::class, 'requestBiodataApproval'])
        ->name('user.profile.request-approval');
    Route::get('/user/profile/create', [ProfileController::class, 'create'])
        ->name('user.profile.create');
    Route::post('/user/profile', [ProfileController::class, 'store'])
        ->name('user.profile.store');
    Route::post('/user/profile/update-location', [ProfileController::class, 'updateLocation'])
        ->name('user.profile.updateLocation');
    Route::get('/user/profile/test-citizen-data', [ProfileController::class, 'testCitizenData'])
        ->name('user.profile.testCitizenData');
    

    // Alur baru edit biodata via BiodataController
    Route::get('/user/profile/family-members', [\App\Http\Controllers\BiodataController::class, 'getProfileFamilyMembers'])
        ->name('profile.family-members');
    Route::get('/user/profile/family-members-with-location', [ProfileController::class, 'getFamilyMembersWithLocation'])
        ->name('profile.family-members-with-location');
    Route::get('/user/profile/debug-user-data', [ProfileController::class, 'debugUserData'])
        ->name('profile.debug-user-data');
    Route::get('/user/profile/citizen/{nik}', [\App\Http\Controllers\BiodataController::class, 'getCitizenForProfile'])
        ->name('profile.citizen.by-nik');
    Route::post('/user/profile/request-biodata-approval', [\App\Http\Controllers\BiodataController::class, 'requestApprovalFromProfile'])
        ->name('profile.biodata.request-approval');

    Route::get('/user/family-member/{nik}/documents', [ProfileController::class, 'getFamilyMemberDocuments'])
        ->name('user.family-member.documents');
    Route::post('/user/family-member/{nik}/upload-document', [ProfileController::class, 'uploadFamilyMemberDocument'])
        ->name('user.family-member.upload-document');
    Route::delete('/user/family-member/{nik}/delete-document/{documentType}', [ProfileController::class, 'deleteFamilyMemberDocument'])
        ->name('user.family-member.delete-document');
    Route::get('/user/family-member/{nik}/document/{documentType}/view', [ProfileController::class, 'viewFamilyMemberDocument'])
        ->name('user.family-member.view-document');

    //Route Kelola Aset

    //api
    Route::get('/api/location-details', [WilayahController::class, 'getLocationDetailsById'])
        ->name('api.location-details');
    Route::get('/api/citizens/all', [WilayahController::class, 'getAllCitizens'])
        ->name('api.all-citizens');
    Route::get('/api/citizens/all', [WilayahController::class, 'getAllCitizens'])
        ->name('api.all-citizens');
    Route::get('/api/provinces', [WilayahController::class, 'getProvinces'])
        ->name('api.provinces');
    Route::get('/api/districts', [WilayahController::class, 'getDistricts'])
        ->name('api.districts');
    Route::get('/api/subdistricts', [WilayahController::class, 'getSubDistricts'])
        ->name('api.subdistricts');
    Route::get('/api/villages', [WilayahController::class, 'getVillages'])
        ->name('api.villages');

    Route::get('/user/kelola-aset', [KelolaAsetController::class, 'index'])
        ->name('user.kelola-aset.index');
    Route::get('/user/kelola-aset/create', [KelolaAsetController::class, 'create'])
        ->name('user.kelola-aset.create');
    Route::post('/user/kelola-aset', [KelolaAsetController::class, 'store'])
        ->name('user.kelola-aset.store');
    Route::get('/user/kelola-aset/{id}/edit', [KelolaAsetController::class, 'edit'])
        ->name('user.kelola-aset.edit');
    Route::put('/user/kelola-aset/{id}', [KelolaAsetController::class, 'update'])
        ->name('user.kelola-aset.update');
    Route::delete('/user/kelola-aset/{id}', [KelolaAsetController::class, 'destroy'])
        ->name('user.kelola-aset.destroy');

    // Route for searching citizen by NIK for asset management
    Route::get('/citizens/search-by-nik/{nik}', [KelolaAsetController::class, 'searchByNik'])
        ->name('citizens.search-by-nik');

    //Route Lapor Desa
    Route::get('/user/laporan-desa', [LaporanDesaController::class, 'index'])
        ->name('user.laporan-desa.index');
    Route::get('/user/laporan-desa/create', [LaporanDesaController::class, 'create'])
        ->name('user.laporan-desa.create');
    Route::post('/user/laporan-desa', [LaporanDesaController::class, 'store'])
        ->name('user.laporan-desa.store');
    Route::get('/user/laporan-desa/{id}', [LaporanDesaController::class, 'show'])
        ->name('user.laporan-desa.show');
    Route::get('/user/laporan-desa/{id}/edit', [LaporanDesaController::class, 'edit'])
        ->name('user.laporan-desa.edit');
    Route::put('/user/laporan-desa/{id}', [LaporanDesaController::class, 'update'])
        ->name('user.laporan-desa.update');
    Route::delete('/user/laporan-desa/{id}', [LaporanDesaController::class, 'destroy'])
        ->name('user.laporan-desa.destroy');
    Route::get('/user/laporan-desa/{id}', [LaporanDesaController::class, 'show'])
        ->name('user.laporan-desa.show');

    //riwayat surat routes
    Route::get('/user/riwayat-surat', [RiwayatSuratController::class, 'index'])
        ->name('user.riwayat-surat.index');

    // Biodata Change (Penduduk)
    Route::get('/user/biodata-change', [UserBiodataChangeController::class, 'index'])
        ->name('user.biodata-change.index');
    Route::get('/user/biodata-change/create', [UserBiodataChangeController::class, 'create'])
        ->name('user.biodata-change.create');
    Route::post('/user/biodata-change', [UserBiodataChangeController::class, 'store'])
        ->name('user.biodata-change.store');
    Route::get('/user/biodata-change/{id}', [UserBiodataChangeController::class, 'show'])
        ->name('user.biodata-change.show');

    // Detail surat routes
    Route::get('/user/surat/skck/{id}/detail', [RiwayatSuratController::class, 'showSKCK'])
        ->name('user.surat.skck.detail');
    Route::get('/user/surat/administrasi/{id}/detail', [RiwayatSuratController::class, 'showAdministrasi'])
        ->name('user.surat.administrasi.detail');
    Route::get('/user/surat/domisili/{id}/detail', [RiwayatSuratController::class, 'showDomisili'])
        ->name('user.surat.domisili.detail');
    Route::get('/user/surat/domisili-usaha/{id}/detail', [RiwayatSuratController::class, 'showDomisiliUsaha'])
        ->name('user.surat.domisili-usaha.detail');
    Route::get('/user/surat/kehilangan/{id}/detail', [RiwayatSuratController::class, 'showKehilangan'])
        ->name('user.surat.kehilangan.detail');
    Route::get('/user/surat/ktp/{id}/detail', [RiwayatSuratController::class, 'showKTP'])
        ->name('user.surat.ktp.detail');
    Route::get('/user/surat/rumah-sewa/{id}/detail', [RiwayatSuratController::class, 'showRumahSewa'])
        ->name('user.surat.rumah-sewa.detail');
    Route::get('/user/surat/keramaian/{id}/detail', [RiwayatSuratController::class, 'showKeramaian'])
        ->name('user.surat.keramaian.detail');
    Route::get('/user/surat/kelahiran/{id}/detail', [RiwayatSuratController::class, 'showKelahiran'])
        ->name('user.surat.kelahiran.detail');
    Route::get('/user/surat/kematian/{id}/detail', [RiwayatSuratController::class, 'showKematian'])
        ->name('user.surat.kematian.detail');
    Route::get('/user/surat/ahli-waris/{id}/detail', [RiwayatSuratController::class, 'showAhliWaris'])
        ->name('user.surat.ahli-waris.detail');

    // Berita Desa routes
    Route::get('/user/berita-desa', [UserBeritaDesaController::class, 'index'])
        ->name('user.berita-desa.index');
    // Penting: letakkan route yang spesifik terlebih dahulu sebelum yang dinamis {id}
    Route::get('/user/berita-desa/create', [UserBeritaDesaController::class, 'create'])
        ->name('user.berita-desa.create');
    Route::post('/user/berita-desa', [UserBeritaDesaController::class, 'store'])
        ->name('user.berita-desa.store');
    Route::post('/user/berita-desa/{id}/send-approval', [UserBeritaDesaController::class, 'sendApproval'])
        ->name('user.berita-desa.send-approval');
    Route::get('/user/berita-desa/{id}', [UserBeritaDesaController::class, 'show'])
        ->name('user.berita-desa.show');
    // Pengumuman user
    Route::get('/user/pengumuman', [UserPengumumanController::class, 'index'])->name('user.pengumuman.index');
    Route::get('/user/pengumuman/{id}', [UserPengumumanController::class, 'show'])->name('user.pengumuman.show');

    Route::get('/profile/family-member/{nik}/document/{type}', [ProfileController::class, 'viewFamilyMemberDocument'])
        ->middleware(['auth:admin_desa']); // atau middleware yang sesuai
});

// User management routes
Route::middleware(['auth'])->group(function () {
    // User CRUD routes for superadmin
    Route::middleware(['role:superadmin'])->prefix('superadmin/datamaster')->group(function () {
        Route::resource('user', UsersController::class, ['as' => 'superadmin.datamaster']);
    });
});

// Fix the fetch-heads-of-family route to use the correct controller
// Route::get('/fetch-heads-of-family', [DataKKController::class, 'fetchHeadsOfFamily'])->name('citizens.heads');
// Route::get('/fetch-all-citizens', [DataKKController::class, 'fetchAllCitizens']);
Route::get('/getFamilyMembers', [DataKKController::class, 'getFamilyMembers'])->name('getFamilyMembers');
Route::get('/superadmin/datakk/{kk_id}/family-members', [DataKKController::class, 'getFamilyMembersByKK'])->name('superadmin.datakk.family-members');
Route::get('/biodata/family-members', [BiodataController::class, 'getFamilyMembers'])->name('biodata.family-members');

// Update the wilayah route to use proper parameters
Route::get('/api/wilayah/provinsi/{id}/kota', [WilayahController::class, 'getKotaByProvinsi'])
    ->name('wilayah.kota')
    ->where('id', '[0-9]+');
Route::get('/api/wilayah/kota/{id}/kecamatan', [WilayahController::class, 'getKecamatanByKota'])
    ->name('wilayah.kecamatan')
    ->where('id', '[0-9]+');
Route::get('/api/wilayah/kecamatan/{id}/kelurahan', [WilayahController::class, 'getDesaByKecamatan'])
    ->name('wilayah.kelurahan')
    ->where('id', '[0-9]+');

// Location data routes (directly from BiodataController)
// Define specific routes for user management location data
Route::get('/location/provinces', [DataKKController::class, 'getProvinces'])->name('location.provinces');
Route::get('/location/districts/{provinceCode}', [DataKKController::class, 'getDistricts'])->name('location.districts');
Route::get('/location/sub-districts/{districtCode}', [DataKKController::class, 'getSubDistricts'])->name('location.sub-districts');
Route::get('/location/villages/{subDistrictCode}', [DataKKController::class, 'getVillages'])->name('location.villages');

// Citizen data routes
Route::get('/citizens/all', [DataKKController::class, 'fetchAllCitizens'])->name('citizens.all');

// New route for administration citizens data
Route::get('/citizens/administrasi', [AdministrasiController::class, 'fetchAllCitizens'])->name('citizens.administrasi');

Route::post('/superadmin/datakk/store-family-members', [DataKKController::class, 'storeFamilyMembers'])
    ->name('superadmin.datakk.store-family-members');

Route::post('/superadmin/biodata/import', [BiodataController::class, 'import'])->name('superadmin.biodata.import');

// Route untuk testing citizen data dengan village_id
Route::get('/test/citizens/{village_id}', function($villageId) {
    $citizenService = app(\App\Services\CitizenService::class);
    $response = $citizenService->getAllCitizensWithHighLimit();

    // Filter berdasarkan village_id
    $citizens = [];
    if (isset($response['data']['citizens'])) {
        $citizens = array_filter($response['data']['citizens'], function($citizen) use ($villageId) {
            $citizenVillageId = $citizen['village_id'] ??
                               $citizen['villages_id'] ??
                               $citizen['sub_district_id'] ??
                               null;
            return $citizenVillageId == $villageId;
        });
    }

    return response()->json([
        'village_id' => $villageId,
        'total_citizens' => count($citizens),
        'sample_citizens' => array_slice($citizens, 0, 5),
        'debug' => [
            'api_response_structure' => array_keys($response),
            'has_citizens' => isset($response['data']['citizens']),
            'total_from_api' => isset($response['data']['citizens']) ? count($response['data']['citizens']) : 0
        ]
    ]);
})->name('test.citizens');

// Route untuk testing struktur data
Route::get('/test/data-structure/{village_id}', function($villageId) {
    $citizenService = app(\App\Services\CitizenService::class);
    $response = $citizenService->getAllCitizensWithHighLimit();

    // Log response structure
    \Log::info('Test data structure API response', [
        'village_id' => $villageId,
        'response_keys' => array_keys($response),
        'has_data' => isset($response['data']),
        'data_type' => isset($response['data']) ? gettype($response['data']) : 'not_set'
    ]);

    // Extract citizens
    $citizens = [];
    if (isset($response['data']) && isset($response['data']['citizens'])) {
        $citizens = $response['data']['citizens'];
    } elseif (isset($response['data']) && is_array($response['data'])) {
        $citizens = $response['data'];
    }

    // Filter by village_id
    $filteredCitizens = [];
    foreach ($citizens as $citizen) {
        $citizenVillageId = $citizen['village_id'] ??
                           $citizen['villages_id'] ??
                           $citizen['sub_district_id'] ??
                           null;

        if ($citizenVillageId == $villageId) {
            $filteredCitizens[] = $citizen;
        }
    }

    return response()->json([
        'village_id' => $villageId,
        'total_citizens' => count($filteredCitizens),
        'sample_citizens' => array_slice($filteredCitizens, 0, 3),
        'debug' => [
            'api_response_structure' => array_keys($response),
            'has_citizens' => isset($response['data']['citizens']),
            'total_from_api' => isset($response['data']['citizens']) ? count($response['data']['citizens']) : 0,
            'sample_original_citizen' => isset($response['data']['citizens']) && !empty($response['data']['citizens']) ? $response['data']['citizens'][0] : null,
            'filtered_structure' => !empty($filteredCitizens) ? array_keys($filteredCitizens[0]) : []
        ]
    ]);
})->name('test.data.structure');

// Route untuk mengakses dokumen keluarga (bisa diakses oleh admin dan superadmin)
Route::middleware(['auth:web'])->group(function () {
    Route::get('/admin/family-member/{nik}/documents', [ProfileController::class, 'getFamilyMemberDocuments'])
        ->name('admin.family-member.documents');
    Route::get('/admin/family-member/{nik}/document/{documentType}/view', [ProfileController::class, 'viewFamilyMemberDocument'])
        ->name('admin.family-member.view-document');
});

// Route untuk clear cache (hanya untuk development/testing)
Route::get('/clear-cache', function() {
    Cache::flush();
    return redirect()->back()->with('success', 'Cache berhasil dibersihkan!');
})->name('clear.cache')->middleware('auth');

// Route untuk force refresh cache (untuk testing)
Route::get('/force-refresh-cache', function() {
    Cache::flush();
    return redirect()->back()->with('success', 'Cache berhasil dibersihkan!');
})->name('force.refresh.cache')->middleware('auth');

// Route untuk clear specific citizen cache
Route::get('/clear-citizen-cache/{nik}', function($nik) {
    $cacheKeys = [
        'admin_citizens_all',
        'citizens_all_*',
        'citizens_search_*'
    ];

    foreach ($cacheKeys as $key) {
        Cache::forget($key);
    }

    return response()->json(['success' => true, 'message' => 'Cache cleared for NIK: ' . $nik]);
})->name('clear.citizen.cache');

// Modifikasi route edit untuk menggunakan nomor KK
Route::get('/admin/desa/datakk/{kk}/edit', [DataKKController::class, 'editByKK'])
    ->name('admin.desa.datakk.edit');
Route::put('/admin/desa/datakk/{kk}', [DataKKController::class, 'updateByKK'])
    ->name('admin.desa.datakk.update');

// Public AJAX routes untuk wilayah (tidak memerlukan authentication)
Route::get('/api/wilayah/kabupaten-by-province', [SuperadminBeritaDesaController::class, 'getKabupatenByProvince'])
    ->name('api.wilayah.kabupaten-by-province');
Route::get('/api/wilayah/kecamatan-by-kabupaten', [SuperadminBeritaDesaController::class, 'getKecamatanByKabupaten'])
    ->name('api.wilayah.kecamatan-by-kabupaten');
Route::get('/api/wilayah/desa-by-kecamatan', [SuperadminBeritaDesaController::class, 'getDesaByKecamatan'])
    ->name('api.wilayah.desa-by-kecamatan');




