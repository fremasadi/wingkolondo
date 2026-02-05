<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\OmzetController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('tokos', TokoController::class);
    Route::resource('bahan-bakus', BahanBakuController::class);
    Route::resource('produks', ProdukController::class);
    Route::resource('produksis', ProduksiController::class);
    Route::patch('produksis/{id}/selesai', [ProduksiController::class, 'selesai'])->name('produksis.selesai');
    Route::resource('pesanans', PesananController::class);
    Route::post('pesanans/{pesanan}/distribusi', [PesananController::class, 'storeDistribusi'])->name('pesanans.distribusi.store');
    Route::put('pesanans/{pesanan}/distribusi', [PesananController::class, 'updateDistribusi'])->name('pesanans.distribusi.update');
    Route::patch('pesanans/{pesanan}/distribusi/selesai', [PesananController::class, 'selesaiDistribusi'])->name('pesanans.distribusi.selesai');
    Route::delete('pesanans/{pesanan}/distribusi', [PesananController::class, 'destroyDistribusi'])->name('pesanans.distribusi.destroy');
    Route::resource('returs', ReturController::class)->except(['edit', 'update', 'destroy']);

    Route::resource('piutangs', PiutangController::class)
    ->only(['index','edit','update']);

    Route::get('/omzet', [OmzetController::class, 'index'])
    ->name('omzet.index');
});

require __DIR__ . '/auth.php';
