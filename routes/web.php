<?php

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

Route::get('/', function () {
    return view('homepage');
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
    Route::get('/superadmin/surat/kematian/{id}/export-pdf', [KematianController::class, 'exportPDF'])->name('superadmin.surat.kematian.export-pdf');

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
    Route::get('/superadmin/surat/keramaian/{id}/export-pdf', [IzinKeramaianController::class, 'exportPDF'])
        ->name('superadmin.surat.keramaian.export-pdf');

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
    Route::get('/superadmin/surat/rumah-sewa/{id}/export-pdf', [RumahSewaController::class, 'exportPDF'])
        ->name('superadmin.surat.rumah-sewa.export-pdf');

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
    Route::get('/surat/pengantar-ktp/{id}/export-pdf', [PengantarKtpController::class, 'exportPDF'])->name('superadmin.surat.pengantar-ktp.export-pdf');

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
    Route::get('/superadmin/datamaster/masterkeperluan/keperluan', [App\Http\Controllers\Superadmin\KeperluanController::class, 'index'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.index');
    Route::get('/superadmin/datamaster/masterkeperluan/keperluan/create', [App\Http\Controllers\Superadmin\KeperluanController::class, 'create'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.create');
    Route::post('/superadmin/datamaster/masterkeperluan/keperluan', [App\Http\Controllers\Superadmin\KeperluanController::class, 'store'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.store');
    Route::get('/superadmin/datamaster/masterkeperluan/keperluan/{keperluan}/edit', [App\Http\Controllers\Superadmin\KeperluanController::class, 'edit'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.edit');
    Route::put('/superadmin/datamaster/masterkeperluan/keperluan/{keperluan}', [App\Http\Controllers\Superadmin\KeperluanController::class, 'update'])
        ->name('superadmin.datamaster.masterkeperluan.keperluan.update');
    Route::delete('/superadmin/datamaster/masterkeperluan/keperluan/{keperluan}', [App\Http\Controllers\Superadmin\KeperluanController::class, 'destroy'])
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
});

// Route untuk admin kabupaten - menggunakan web guard
Route::middleware(['auth:web', 'role:admin kabupaten'])->group(function () {
    Route::get('/admin/kabupaten/index', [DashboardController::class, 'indexKabupaten'])
        ->name('admin.kabupaten.index');

    // Admin Kabupaten Profile routes - Ensure these are correctly defined
    Route::get('/admin/kabupaten/profile', [App\Http\Controllers\adminKabupaten\ProfileController::class, 'index'])
        ->name('admin.kabupaten.profile.index');
    Route::get('/admin/kabupaten/profile/edit', [App\Http\Controllers\adminKabupaten\ProfileController::class, 'edit'])
        ->name('admin.kabupaten.profile.edit');
    Route::put('/admin/kabupaten/profile/update', [App\Http\Controllers\adminKabupaten\ProfileController::class, 'update'])
        ->name('admin.kabupaten.profile.update');
    Route::post('/admin/kabupaten/profile/update-photo', [App\Http\Controllers\adminKabupaten\ProfileController::class, 'updatePhoto'])
        ->name('admin.kabupaten.profile.update-photo');
});

// Route untuk admin desa - menggunakan web guard
Route::middleware(['auth:web', 'role:admin desa'])->group(function () {
    Route::get('/admin/desa/index', [DashboardController::class, 'indexDesa'])
        ->name('admin.desa.index');
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
    Route::get('/user/profile/create', [ProfileController::class, 'create'])
        ->name('user.profile.create');
    Route::post('/user/profile', [ProfileController::class, 'store'])
        ->name('user.profile.store');
    Route::post('/user/profile/update-location', [ProfileController::class, 'updateLocation'])
        ->name('user.profile.updateLocation');

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

});

// User management routes
Route::middleware(['auth'])->group(function () {
    // User CRUD routes for superadmin
    Route::middleware(['role:superadmin'])->prefix('superadmin/datamaster')->group(function () {
        Route::resource('user', \App\Http\Controllers\UsersController::class, ['as' => 'superadmin.datamaster']);
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





