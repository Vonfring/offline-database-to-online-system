<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HargaPlastikController;
use App\Http\Controllers\ImporController;
use App\Http\Controllers\JenisPlastikController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

/*
| Semua halaman di bawah ini hanya dapat diakses setelah login.
| peran:staf  → staf dan admin (admin mewarisi semua hak akses staf)
| peran:admin → khusus admin
*/
Route::middleware(['auth', 'peran:staf'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // Data master
    Route::resource('supplier', SupplierController::class);
    Route::resource('pembeli', PembeliController::class)->parameters(['pembeli' => 'pembeli']);
});

Route::middleware(['auth', 'peran:admin'])->group(function () {
    // Jenis plastik & riwayat harga
    Route::resource('jenis-plastik', JenisPlastikController::class)->parameters(['jenis-plastik' => 'jenisPlastik']);
    Route::post('jenis-plastik/{jenisPlastik}/harga', [HargaPlastikController::class, 'store'])->name('jenis-plastik.harga.store');
    Route::put('jenis-plastik/{jenisPlastik}/harga/{harga}', [HargaPlastikController::class, 'update'])->name('jenis-plastik.harga.update');
    Route::delete('jenis-plastik/{jenisPlastik}/harga/{harga}', [HargaPlastikController::class, 'destroy'])->name('jenis-plastik.harga.destroy');

    // Impor data offline (migrasi)
    Route::prefix('impor')->name('impor.')->controller(ImporController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/template/{jenis}', 'template')->name('template');
        Route::post('/unggah', 'unggah')->name('unggah');
        Route::get('/pemetaan', 'pemetaan')->name('pemetaan');
        Route::post('/pemetaan', 'simpanPemetaan')->name('pemetaan.simpan');
        Route::get('/pratinjau', 'pratinjau')->name('pratinjau');
        Route::post('/konfirmasi', 'konfirmasi')->name('konfirmasi');
        Route::post('/batal', 'batal')->name('batal');
        Route::get('/riwayat', 'riwayat')->name('riwayat');
        Route::get('/riwayat/{logImpor}', 'hasil')->name('hasil');
    });
});

require __DIR__.'/auth.php';
