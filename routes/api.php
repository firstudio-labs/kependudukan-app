<?php

use App\Http\Controllers\Api\LaporDesaApiController;
use App\Http\Controllers\Api\LaporDesaController;
use App\Http\Middleware\ApiTokenOwnerMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KelolaAsetController;
use App\Http\Controllers\Api\AuthController;

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
        Route::get('/laporan-desa', [LaporDesaController::class, 'index'])
            ->name('user.laporan-desa.index');
        Route::get('/laporan-desa/create', [LaporDesaController::class, 'create'])
            ->name('user.laporan-desa.create');
        Route::post('/laporan-desa', [LaporDesaController::class, 'store'])
            ->name('user.laporan-desa.store');
        Route::get('/laporan-desa/{id}/edit', [LaporDesaController::class, 'edit'])
            ->name('user.laporan-desa.edit');
        Route::put('/laporan-desa/{id}', [LaporDesaController::class, 'update'])
            ->name('user.laporan-desa.update');
        Route::delete('/laporan-desa/{id}', [LaporDesaController::class, 'destroy'])
            ->name('user.laporan-desa.destroy');

    });
});






Route::fallback(function () {
    return response()->json(['message' => 'API endpoint not found'], 404);
});

