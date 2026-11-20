<?php

namespace App\Services;

use GdImage;
use Illuminate\Cache\DatabaseStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Lottery;
use Illuminate\Support\Str;

/**
 * CAPTCHA gambar sederhana tanpa layanan luar. Jawaban disimpan sebagai hash di cache dan hanya bisa dipakai sekali.
 */
class CaptchaService
{
    public const PANJANG = 5;

    public const BERLAKU_MENIT = 10;

    // Huruf yang mirip angka atau huruf lain (B/8, G/6, I/1, L, O/0, S/5, Z/2) tidak dipakai agar mudah dibaca.
    private const ALFABET = 'ACDEFHJKMNPQRTUVWXY23456789';

    private const PREFIX = 'captcha:';

    // Rata-rata 1 dari 50 pembuatan CAPTCHA ikut membersihkan sisa CAPTCHA kedaluwarsa, tanpa perlu cron.
    private const PELUANG_BERSIH = [1, 50];

    private const LEBAR = 200;

    private const TINGGI = 64;

    /**
     * @return array{kunci: string, gambar: string, berlaku_detik: int}
     */
    public function buat(?string $kode = null): array
    {
        $kode ??= $this->kodeAcak();
        $kunci = (string) Str::uuid();

        Cache::put(self::PREFIX.$kunci, $this->hash($kode), now()->addMinutes(self::BERLAKU_MENIT));

        Lottery::odds(...self::PELUANG_BERSIH)->winner(fn () => $this->bersihkanKedaluwarsa())->choose();

        return [
            'kunci' => $kunci,
            'gambar' => 'data:image/png;base64,'.base64_encode($this->gambar($kode)),
            'berlaku_detik' => self::BERLAKU_MENIT * 60,
        ];
    }

    /**
     * Memeriksa jawaban lalu menghapus CAPTCHA, sehingga satu kode tidak bisa ditebak berulang kali.
     */
    public function periksa(?string $kunci, ?string $jawaban): bool
    {
        if (blank($kunci) || blank($jawaban)) {
            return false;
        }

        $hash = Cache::pull(self::PREFIX.$kunci);

        return is_string($hash) && hash_equals($hash, $this->hash($jawaban));
    }

    /**
     * Menghapus CAPTCHA yang dibuka tetapi tidak pernah dikirim. Cache database tidak menghapus baris kedaluwarsa sendiri.
     *
     * @return int Jumlah baris yang dihapus.
     */
    public function bersihkanKedaluwarsa(): int
    {
        $toko = Cache::getStore();

        if (! $toko instanceof DatabaseStore) {
            return 0;
        }

        return $toko->getConnection()
            ->table(config('cache.stores.database.table', 'cache'))
            ->where('key', 'like', addcslashes($toko->getPrefix().self::PREFIX, '%_\\').'%')
            ->where('expiration', '<=', now()->getTimestamp())
            ->delete();
    }

    private function hash(string $kode): string
    {
        return hash_hmac('sha256', Str::upper(preg_replace('/\s+/', '', $kode)), (string) config('app.key'));
    }

    private function kodeAcak(): string
    {
        $kode = '';

        for ($i = 0; $i < self::PANJANG; $i++) {
            $kode .= self::ALFABET[random_int(0, strlen(self::ALFABET) - 1)];
        }

        return $kode;
    }

    private function gambar(string $kode): string
    {
        $kanvas = imagecreatetruecolor(self::LEBAR, self::TINGGI);
        imagefill($kanvas, 0, 0, imagecolorallocate($kanvas, random_int(236, 248), random_int(240, 250), random_int(244, 252)));

        $this->coretan($kanvas, 7, 150, 210);

        for ($i = 0; $i < 250; $i++) {
            imagesetpixel($kanvas, random_int(0, self::LEBAR - 1), random_int(0, self::TINGGI - 1), imagecolorallocate($kanvas, random_int(120, 200), random_int(120, 200), random_int(120, 200)));
        }

        $jarak = (self::LEBAR - 20) / max(1, strlen($kode));

        foreach (str_split($kode) as $urutan => $huruf) {
            $gambarHuruf = $this->huruf($huruf);
            $x = (int) (12 + $urutan * $jarak + random_int(-3, 3));
            $y = (int) ((self::TINGGI - imagesy($gambarHuruf)) / 2 + random_int(-4, 4));

            imagecopy($kanvas, $gambarHuruf, $x, $y, 0, 0, imagesx($gambarHuruf), imagesy($gambarHuruf));
        }

        $this->coretan($kanvas, 2, 60, 130);

        ob_start();
        imagepng($kanvas);

        return (string) ob_get_clean();
    }

    /**
     * Huruf bawaan GD diperbesar lalu dimiringkan acak, karena font TTF belum tentu ada di shared hosting.
     */
    private function huruf(string $huruf): GdImage
    {
        $kecil = $this->transparan(10, 16);
        imagestring($kecil, 5, 1, 0, $huruf, imagecolorallocate($kecil, random_int(10, 70), random_int(40, 90), random_int(70, 130)));

        $besar = $this->transparan(30, 46);
        imagecopyresampled($besar, $kecil, 0, 0, 0, 0, 30, 46, 10, 16);

        $miring = imagerotate($besar, random_int(-22, 22), imagecolorallocatealpha($besar, 0, 0, 0, 127));
        imagesavealpha($miring, true);

        return $miring;
    }

    private function transparan(int $lebar, int $tinggi): GdImage
    {
        $gambar = imagecreatetruecolor($lebar, $tinggi);
        imagealphablending($gambar, false);
        imagefill($gambar, 0, 0, imagecolorallocatealpha($gambar, 0, 0, 0, 127));
        imagealphablending($gambar, true);
        imagesavealpha($gambar, true);

        return $gambar;
    }

    private function coretan(GdImage $kanvas, int $jumlah, int $gelap, int $terang): void
    {
        imagesetthickness($kanvas, 2);

        for ($i = 0; $i < $jumlah; $i++) {
            $warna = imagecolorallocate($kanvas, random_int($gelap, $terang), random_int($gelap, $terang), random_int($gelap, $terang));
            imageline($kanvas, random_int(0, 40), random_int(0, self::TINGGI), random_int(self::LEBAR - 40, self::LEBAR), random_int(0, self::TINGGI), $warna);
        }

        imagesetthickness($kanvas, 1);
    }
}
