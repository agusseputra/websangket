<?php

use App\Http\Controllers\ArtikelPublikController;
use Illuminate\Support\Facades\Route;

Route::get('/listarticle/{kategori?}', [ArtikelPublikController::class, 'daftar'])->name('artikel.daftar');
Route::get('/detailarticle/{id}/{slug?}', [ArtikelPublikController::class, 'detail'])->whereNumber('id')->name('artikel.detail');
Route::view('/profil', 'profil.index')->name('profil');
Route::view('/lpd', 'lpd.index')->name('lpd');
Route::get('/sitemap.xml', [ArtikelPublikController::class, 'sitemap'])->name('sitemap');

Route::view('/{any?}', 'app')->where('any', '^(?!api|sanctum|up|listarticle|detailarticle|sitemap\.xml|profil$|lpd$|assets/).*$');
