<?php

namespace Database\Seeders;

use App\Models\Krama;
use App\Models\Perumahan;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Data fiktif untuk mencoba aplikasi di lokal. Tidak dipanggil oleh DatabaseSeeder.
 * Hapus dengan: php artisan krama:hapus-contoh
 */
class ContohKramaSeeder extends Seeder
{
    public const PENANDA = 'Data contoh';

    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Data contoh tidak boleh dibuat di server produksi.');
        }

        $perumahan = Perumahan::pluck('id', 'nama');
        $penanda = User::where('role', 'admin')->value('id');

        foreach ($this->daftar() as $indeks => $data) {
            [$nama, $status, $nik, $asal, $namaPerumahan, $nomor, $pekerjaan, $lokasi] = $data;

            $krama = Krama::firstOrNew(['nama' => $nama, 'keterangan' => self::PENANDA]);

            $krama->fill([
                'nik' => $nik,
                'status' => $status,
                'asal' => $asal,
                'pekerjaan' => $pekerjaan,
                'perumahan_id' => $namaPerumahan ? $perumahan[$namaPerumahan] : null,
                'nomor_rumah' => $nomor,
                'jumlah_penghuni' => 2 + $indeks % 4,
                'no_hp' => '0800000'.str_pad((string) ($indeks + 1), 5, '0', STR_PAD_LEFT),
                'latitude' => $lokasi?->lat ?? null,
                'longitude' => $lokasi?->lng ?? null,
                'lokasi_ditandai_oleh' => $lokasi ? $penanda : null,
                'lokasi_ditandai_at' => $lokasi ? now() : null,
            ])->save();
        }
    }

    /**
     * Awalan NIK 9999 tidak dipakai wilayah mana pun, sehingga jelas fiktif.
     */
    private function daftar(): array
    {
        $titik = fn (float $lat, float $lng) => (object) ['lat' => $lat, 'lng' => $lng];

        return [
            // Data lengkap, lokasi sudah ditandai.
            ['Contoh Wayan Artha', 'pengampel', '9999010101800001', 'Sukasada', 'RT 2', '12', 'Wiraswasta', $titik(-8.1398, 115.0894)],
            ['Contoh Made Budi', 'pengampel', '9999010202810002', 'Singaraja', 'RT 3', '7', 'PNS', $titik(-8.1402, 115.0901)],
            ['Contoh Nyoman Cakra', 'pemirak', '9999010303820003', 'Sukasada', 'RT 2', null, 'Petani', $titik(-8.1409, 115.0887)],
            ['Contoh Ketut Dharma', 'pemirak', '9999010404830004', 'Seririt', 'RT 4 Pugpug', '3', 'Swasta', $titik(-8.1415, 115.0908)],
            ['Contoh Putu Eka', 'krama_tamiu', '9999014505840005', 'Tejakula', 'Griya Adi Jaya 1', 'A5', 'Guru', $titik(-8.1391, 115.0912)],
            ['Contoh Kadek Fajar', 'krama_tamiu', '9999010606850006', 'Gerokgak', 'Krisna Graha 1', 'B2', 'Swasta', $titik(-8.1422, 115.0896)],
            ['Contoh Luh Gita', 'tamiu', '9999014707860007', 'Banyuwangi', 'Kanaya', 'C9', 'Perawat', $titik(-8.1387, 115.0879)],
            ['Contoh Agus Hartono', 'tamiu', '9999010808870008', 'Jember', 'Baruna', '19', 'Wiraswasta', $titik(-8.1418, 115.0883)],

            // Data lengkap, lokasi belum ditandai sehingga pembayaran terkunci.
            ['Contoh Gede Indra', 'krama_tamiu', '9999010909880009', 'Busungbiru', 'Griya Adi Jaya 2', 'D4', 'Swasta', null],
            ['Contoh Komang Jaya', 'krama_tamiu', '9999011010890010', 'Tianyar', 'Krisna Graha 2', 'E7', 'Pensiunan', null],
            ['Contoh Ni Luh Kartika', 'pengampel', '9999015111900011', 'Sukasada', 'Griya Jro', 'B1', 'Mahasiswa', null],
            ['Contoh Budi Lesmana', 'tamiu', '9999011212910012', 'Surabaya', 'Padi Permai', '36', 'Swasta', null],

            // Data pending: NIK kosong, NIK tidak valid, asal kosong, atau perumahan kosong.
            ['Contoh Made Mertha', 'krama_tamiu', null, 'Sukasada', 'RT 3', '21', null, null],
            ['Contoh Wayan Nada', 'pengampel', '999901', 'Banjar', 'RT 2', null, null, null],
            ['Contoh Ketut Oka', 'krama_tamiu', '9999019913920015', 'Bondalem', 'Bukit Awangga', '3', null, null],
            ['Contoh Putu Pradnya', 'tamiu', '9999010101930016', null, 'Kanaya', 'A3', null, null],
            ['Contoh Kadek Rai', 'krama_tamiu', '9999010202940017', 'Munduk', null, null, 'Swasta', null],
            ['Contoh Komang Surya', 'pemirak', null, null, null, null, null, null],
        ];
    }
}
