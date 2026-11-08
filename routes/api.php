<?php

use App\Http\Controllers\Api\ArtikelTerbaruController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CekTunggakanController;
use App\Http\Controllers\Api\ImporKramaController;
use App\Http\Controllers\Api\ImporPembayaranController;
use App\Http\Controllers\Api\KramaController;
use App\Http\Controllers\Api\LaporanWargaController;
use App\Http\Controllers\Api\PeriodeIuranController;
use App\Http\Controllers\Api\PerumahanController;
use App\Http\Controllers\Api\PetaController;
use App\Http\Controllers\Api\RingkasanController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/cek-tunggakan', CekTunggakanController::class)->middleware('throttle:10,1');
Route::get('/artikel-terbaru', ArtikelTerbaruController::class)->middleware('throttle:60,1');
Route::post('/laporan-warga', [LaporanWargaController::class, 'store'])->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/admin/ringkasan', [RingkasanController::class, 'admin'])->middleware('role:admin');
    Route::get('/petugas/ringkasan', [RingkasanController::class, 'petugas'])->middleware('role:admin,petugas');

    Route::middleware('role:admin,petugas')->group(function () {
        Route::get('/perumahan', [PerumahanController::class, 'index']);
        Route::get('/krama', [KramaController::class, 'index']);
        Route::post('/krama', [KramaController::class, 'store']);
        Route::get('/krama/{krama}', [KramaController::class, 'show']);
        Route::put('/krama/{krama}', [KramaController::class, 'update']);
        Route::get('/krama/{krama}/nik', [KramaController::class, 'nik'])->middleware('throttle:30,1');
        Route::put('/krama/{krama}/lokasi', [KramaController::class, 'lokasi']);
        Route::get('/krama/{krama}/tagihan', [TagihanController::class, 'milikKrama']);
        Route::post('/krama/{krama}/tagihan/siapkan', [TagihanController::class, 'siapkan']);
        Route::post('/krama/{krama}/bayar', [TagihanController::class, 'bayar']);
        Route::get('/tunggakan', [TagihanController::class, 'tunggakan']);
        Route::get('/tagihan', [TagihanController::class, 'index']);
        Route::get('/periode-tahun', [PeriodeIuranController::class, 'tahun']);
        Route::get('/pilihan-petugas', [UserController::class, 'pilihanPetugas']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::delete('/krama/{krama}', [KramaController::class, 'destroy']);
        Route::post('/perumahan', [PerumahanController::class, 'store']);
        Route::put('/perumahan/{perumahan}', [PerumahanController::class, 'update']);
        Route::delete('/perumahan/{perumahan}', [PerumahanController::class, 'destroy']);
        Route::get('/impor-krama', [ImporKramaController::class, 'status']);
        Route::post('/impor-krama/pratinjau', [ImporKramaController::class, 'pratinjau']);
        Route::post('/impor-krama', [ImporKramaController::class, 'simpan']);
        Route::delete('/impor-krama', [ImporKramaController::class, 'batal']);
        Route::get('/impor-pembayaran', [ImporPembayaranController::class, 'status']);
        Route::post('/impor-pembayaran/pratinjau', [ImporPembayaranController::class, 'pratinjau']);
        Route::post('/impor-pembayaran', [ImporPembayaranController::class, 'simpan']);
        Route::delete('/impor-pembayaran', [ImporPembayaranController::class, 'batal']);

        Route::get('/peta', [PetaController::class, 'index']);

        Route::get('/laporan-warga', [LaporanWargaController::class, 'index']);
        Route::put('/laporan-warga/{laporan}/status', [LaporanWargaController::class, 'ubahStatus']);
        Route::get('/laporan-warga/{laporan}/nik', [LaporanWargaController::class, 'nik'])->middleware('throttle:30,1');

        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);

        Route::get('/periode', [PeriodeIuranController::class, 'index']);
        Route::post('/periode', [PeriodeIuranController::class, 'store']);
        Route::put('/periode/{periode}', [PeriodeIuranController::class, 'update']);
        Route::get('/periode/{periode}/pratinjau', [PeriodeIuranController::class, 'pratinjau']);
        Route::post('/periode/{periode}/generate', [PeriodeIuranController::class, 'generate']);

        Route::post('/tagihan/validasi', [TagihanController::class, 'validasi']);
        Route::post('/tagihan/{tagihan}/koreksi', [TagihanController::class, 'koreksi']);
        Route::post('/tagihan/{tagihan}/dispensasi', [TagihanController::class, 'dispensasi']);
    });
});
