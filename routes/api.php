<?php

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

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
});

// Fallback route for undefined API routes
Route::fallback(function () {
    return response()->json(['message' => 'API endpoint not found'], 404);
});

Route::prefix('user')->group(function () {
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
});

