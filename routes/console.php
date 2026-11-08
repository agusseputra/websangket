<?php

use App\Models\Krama;
use App\Models\Tagihan;
use App\Services\ImporArtikelService;
use Database\Seeders\ContohKramaSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('krama:hapus-contoh', function () {
    $krama = Krama::withTrashed()->where('keterangan', ContohKramaSeeder::PENANDA);
    $jumlah = (clone $krama)->count();

    DB::transaction(function () use ($krama) {
        Tagihan::whereIn('krama_id', (clone $krama)->select('id'))->delete();
        $krama->forceDelete();
    });

    $this->info("{$jumlah} krama contoh dihapus.");
})->purpose('Menghapus data krama contoh beserta tagihannya');

Artisan::command('artikel:impor {kategori : Path dump SQL tabel categories} {artikel : Path dump SQL tabel articles}', function (ImporArtikelService $impor) {
    try {
        $jumlah = $impor->impor($this->argument('kategori'), $this->argument('artikel'));
    } catch (RuntimeException $galat) {
        $this->error($galat->getMessage());

        return 1;
    }

    $this->info("{$jumlah['categories']} kategori dan {$jumlah['articles']} artikel diimpor.");

    return 0;
})->purpose('Mengimpor kategori dan artikel dari dump SQL web lama');

Artisan::command('artikel:ekstrak-gambar', function (ImporArtikelService $impor) {
    $jumlah = $impor->ekstrakGambarKonten();

    $this->info("{$jumlah['gambar']} gambar dari {$jumlah['artikel']} artikel dipindahkan ke public/".ImporArtikelService::FOLDER_GAMBAR_KONTEN.'.');
})->purpose('Memindahkan gambar base64 di isi artikel menjadi file');
