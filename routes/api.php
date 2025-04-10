<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KelolaAsetController;


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

