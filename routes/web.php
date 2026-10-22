<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriObatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::middleware(['auth'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Kategori Obat (Modal API)
    Route::get('/kategori-obat', [KategoriObatController::class, 'index'])->name('kategori-obat.index');
    Route::post('/kategori-obat', [KategoriObatController::class, 'store'])->name('kategori-obat.store');
    Route::put('/kategori-obat/{kategoriObat}', [KategoriObatController::class, 'update'])->name('kategori-obat.update');
    Route::delete('/kategori-obat/{kategoriObat}', [KategoriObatController::class, 'destroy'])->name('kategori-obat.destroy');

    // 3. Master Data Obat & Active Batches
    Route::get('/obat/{obat}/batches', [ObatController::class, 'getActiveBatches'])->name('obat.batches');
    Route::resource('obat', ObatController::class);

    // 4. Data Supplier
    Route::resource('supplier', SupplierController::class);

    // 5. Mutasi & Transaksi Stok
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::get('/stok/masuk', [StokController::class, 'masuk'])->name('stok.masuk');
    Route::post('/stok/masuk', [StokController::class, 'storeMasuk'])->name('stok.store-masuk');
    Route::get('/stok/keluar', [StokController::class, 'keluar'])->name('stok.keluar');
    Route::post('/stok/keluar', [StokController::class, 'storeKeluar'])->name('stok.store-keluar');

    // 6. Admin Only Area
    Route::middleware(['role:admin'])->group(function () {
        // Kelola Pengguna
        Route::resource('pengguna', UserController::class);

        // Laporan & Export
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/print', [LaporanController::class, 'printView'])->name('laporan.print');
        Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('laporan.export-csv');
    });
});

require __DIR__.'/settings.php';
