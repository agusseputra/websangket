<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * Mengimpor kategori dan artikel dari dump phpMyAdmin web lama.
 *
 * Nilai dibaca langsung dari teks INSERT agar dump MySQL tidak perlu dijalankan apa adanya,
 * dan impor ulang hanya memperbarui baris dengan id yang sama. Waktu di dump tercatat dalam UTC
 * (SET time_zone = "+00:00") sehingga diubah ke zona waktu aplikasi.
 */
class ImporArtikelService
{
    public const FOLDER_GAMBAR_KONTEN = 'assets/article/konten';

    private const KOLOM = [
        'categories' => ['id', 'name', 'created_at', 'updated_at'],
        'articles' => ['id', 'title', 'content', 'image', 'category_id', 'created_at', 'updated_at'],
    ];

    /**
     * @return array{categories: int, articles: int}
     */
    public function impor(string $fileKategori, string $fileArtikel): array
    {
        $kategori = array_map($this->keZonaAplikasi(...), $this->bacaBaris($fileKategori, 'categories'));
        $artikel = array_map($this->keZonaAplikasi(...), $this->bacaBaris($fileArtikel, 'articles'));

        $idKategori = array_column($kategori, 'id');
        $tanpaKategori = array_filter($artikel, fn (array $baris) => ! in_array($baris['category_id'], $idKategori));

        if ($tanpaKategori !== []) {
            throw new RuntimeException('Artikel id '.implode(', ', array_column($tanpaKategori, 'id')).' memakai kategori yang tidak ada di dump kategori.');
        }

        DB::transaction(function () use ($kategori, $artikel) {
            DB::table('categories')->upsert($kategori, ['id'], ['name', 'created_at', 'updated_at']);

            // Satu per satu karena isi artikel dengan gambar base64 bisa berukuran beberapa MB.
            foreach ($artikel as $baris) {
                DB::table('articles')->upsert([$baris], ['id'], ['title', 'content', 'image', 'category_id', 'created_at', 'updated_at']);
            }
        });

        return ['categories' => count($kategori), 'articles' => count($artikel)];
    }

    /**
     * Memindahkan gambar base64 di isi artikel menjadi file agar halaman ringan, lalu mengganti src-nya.
     *
     * @return array{artikel: int, gambar: int}
     */
    public function ekstrakGambarKonten(): array
    {
        $folder = public_path(self::FOLDER_GAMBAR_KONTEN);
        File::ensureDirectoryExists($folder);
        $jumlah = ['artikel' => 0, 'gambar' => 0];

        // Id diambil lebih dulu karena isi yang sudah diubah keluar dari kondisi pencarian; isi dibaca satu per satu agar memori tetap kecil.
        $daftarId = DB::table('articles')->where('content', 'like', '%data:image/%')->orderBy('id')->pluck('id');

        foreach ($daftarId as $id) {
            $artikel = DB::table('articles')->where('id', $id)->first(['id', 'content']);
            $urutan = 0;

            $isi = preg_replace_callback('#src=(["\'])data:image/(png|jpe?g|gif|webp);base64,([A-Za-z0-9+/=\s]++)\1#', function (array $cocok) use ($artikel, $folder, &$urutan) {
                $biner = base64_decode(preg_replace('/\s+/', '', $cocok[3]), true);

                if ($biner === false) {
                    return $cocok[0];
                }

                $nama = $artikel->id.'-'.++$urutan.'.'.($cocok[2] === 'jpeg' ? 'jpg' : $cocok[2]);
                File::put($folder.'/'.$nama, $biner);

                return 'src="/'.self::FOLDER_GAMBAR_KONTEN.'/'.$nama.'"';
            }, $artikel->content);

            if ($isi === null) {
                throw new RuntimeException("Gambar di artikel id {$artikel->id} gagal dibaca: ".preg_last_error_msg().'.');
            }

            if ($urutan > 0) {
                DB::table('articles')->where('id', $artikel->id)->update(['content' => $isi]);
                $jumlah['artikel']++;
                $jumlah['gambar'] += $urutan;
            }
        }

        return $jumlah;
    }

    /**
     * @param  array<string, string|int|null>  $baris
     * @return array<string, string|int|null>
     */
    private function keZonaAplikasi(array $baris): array
    {
        foreach (['created_at', 'updated_at'] as $kolom) {
            if ($baris[$kolom] !== null) {
                $baris[$kolom] = Carbon::parse($baris[$kolom], 'UTC')->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s');
            }
        }

        return $baris;
    }

    /**
     * @return list<array<string, string|int|null>>
     */
    public function bacaBaris(string $path, string $tabel): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException("File {$path} tidak ditemukan atau tidak bisa dibaca.");
        }

        $sql = file_get_contents($path);

        $pola = '/INSERT INTO `'.$tabel.'` \(([^)]*)\) VALUES\s*/';
        $posisi = 0;
        $hasil = [];

        // phpMyAdmin memecah data besar menjadi beberapa perintah INSERT; baca semuanya berurutan.
        while (preg_match($pola, $sql, $cocok, PREG_OFFSET_CAPTURE, $posisi)) {
            $kolom = array_map(fn (string $nama) => trim($nama, ' `'), explode(',', $cocok[1][0]));

            if ($kolom !== self::KOLOM[$tabel]) {
                throw new RuntimeException("Kolom tabel {$tabel} di dump tidak sesuai: ".implode(', ', $kolom).'.');
            }

            $posisi = $cocok[0][1] + strlen($cocok[0][0]);

            while (true) {
                $hasil[] = array_combine($kolom, $this->bacaTuple($sql, $posisi));

                $this->lewatiSpasi($sql, $posisi);
                $pemisah = $sql[$posisi++] ?? '';

                if ($pemisah === ';') {
                    break;
                }

                if ($pemisah !== ',') {
                    throw new RuntimeException("Format dump {$tabel} tidak dikenali di posisi {$posisi}.");
                }

                $this->lewatiSpasi($sql, $posisi);
            }
        }

        if ($hasil === []) {
            throw new RuntimeException("Perintah INSERT untuk tabel {$tabel} tidak ditemukan di {$path}.");
        }

        return $hasil;
    }

    /**
     * @return list<string|int|null>
     */
    private function bacaTuple(string $sql, int &$posisi): array
    {
        if (($sql[$posisi] ?? '') !== '(') {
            throw new RuntimeException("Tuple tidak diawali kurung di posisi {$posisi}.");
        }

        $posisi++;
        $nilai = [];

        while (true) {
            $this->lewatiSpasi($sql, $posisi);
            $nilai[] = $this->bacaNilai($sql, $posisi);
            $this->lewatiSpasi($sql, $posisi);

            $karakter = $sql[$posisi++] ?? '';

            if ($karakter === ')') {
                return $nilai;
            }

            if ($karakter !== ',') {
                throw new RuntimeException("Pemisah nilai tidak dikenali di posisi {$posisi}.");
            }
        }
    }

    private function bacaNilai(string $sql, int &$posisi): string|int|null
    {
        if (($sql[$posisi] ?? '') === "'") {
            return $this->bacaTeks($sql, $posisi);
        }

        preg_match('/\G[^,)\s]+/', $sql, $cocok, 0, $posisi);
        $token = $cocok[0] ?? '';
        $posisi += strlen($token);

        if (strtoupper($token) === 'NULL') {
            return null;
        }

        if (! preg_match('/^-?\d+$/', $token)) {
            throw new RuntimeException("Nilai tidak dikenali: {$token}.");
        }

        return (int) $token;
    }

    /**
     * Membaca string MySQL termasuk escape garis miring terbalik.
     */
    private function bacaTeks(string $sql, int &$posisi): string
    {
        static $escape = ['0' => "\0", 'b' => "\x08", 'n' => "\n", 'r' => "\r", 't' => "\t", 'Z' => "\x1A"];

        $posisi++;
        $hasil = '';
        $panjang = strlen($sql);

        while ($posisi < $panjang) {
            $akhir = strcspn($sql, "\\'", $posisi);
            $hasil .= substr($sql, $posisi, $akhir);
            $posisi += $akhir;
            $karakter = $sql[$posisi] ?? '';

            if ($karakter === '\\') {
                $berikut = $sql[$posisi + 1] ?? '';
                $hasil .= $escape[$berikut] ?? $berikut;
                $posisi += 2;

                continue;
            }

            if ($karakter === "'") {
                if (($sql[$posisi + 1] ?? '') === "'") {
                    $hasil .= "'";
                    $posisi += 2;

                    continue;
                }

                $posisi++;

                return $hasil;
            }
        }

        throw new RuntimeException('Teks tidak ditutup tanda kutip.');
    }

    private function lewatiSpasi(string $sql, int &$posisi): void
    {
        $posisi += strspn($sql, " \t\r\n", $posisi);
    }
}
