<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

// Akun dibuat oleh admin (menu Pengaturan), jadi tidak ada pendaftaran mandiri.
Route::middleware('guest')->group(function () {
    Route::get('masuk', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('masuk', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::put('kata-sandi', [PasswordController::class, 'update'])->name('password.update');
    Route::post('keluar', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
