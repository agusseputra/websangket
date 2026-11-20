<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Menyimpan gambar berita di public/assets seperti web lama, agar tetap jalan di shared hosting tanpa symlink storage.
 */
class GambarBeritaService
{
    public const FOLDER_SAMPUL = 'article';

    public const FOLDER_KONTEN = 'article/konten';

    /**
     * Menyimpan foto sampul dan mengembalikan path relatif terhadap public/assets, misalnya "article/abc.jpg".
     */
    public function simpanSampul(UploadedFile $file): string
    {
        return $this->simpan($file, self::FOLDER_SAMPUL);
    }

    /**
     * Menyimpan gambar di dalam isi berita dan mengembalikan URL relatif, misalnya "/assets/article/konten/abc.jpg".
     */
    public function simpanKonten(UploadedFile $file): string
    {
        return '/assets/'.$this->simpan($file, self::FOLDER_KONTEN);
    }

    /**
     * Gambar yang ditempel langsung ke editor berbentuk base64; diubah menjadi file agar isi berita tetap ringan.
     */
    public function pindahkanBase64(string $html): string
    {
        $folder = public_path('assets/'.self::FOLDER_KONTEN);

        return preg_replace_callback('#src=(["\'])data:image/(png|jpe?g|gif|webp);base64,([A-Za-z0-9+/=\s]++)\1#i', function (array $cocok) use ($folder) {
            $biner = base64_decode(preg_replace('/\s+/', '', $cocok[3]), true);

            if ($biner === false || @getimagesizefromstring($biner) === false) {
                return 'src=""';
            }

            File::ensureDirectoryExists($folder);
            $nama = Str::random(32).'.'.(strtolower($cocok[2]) === 'jpeg' ? 'jpg' : strtolower($cocok[2]));
            File::put($folder.'/'.$nama, $biner);

            return 'src="/assets/'.self::FOLDER_KONTEN.'/'.$nama.'"';
        }, $html) ?? $html;
    }

    /**
     * Menghapus file sampul lama. Hanya file di folder sampul berita yang boleh dihapus.
     */
    public function hapusSampul(?string $path): void
    {
        if (blank($path) || ! preg_match('#^'.self::FOLDER_SAMPUL.'/[A-Za-z0-9_.-]+$#', $path)) {
            return;
        }

        File::delete(public_path('assets/'.$path));
    }

    private function simpan(UploadedFile $file, string $folder): string
    {
        $nama = Str::random(40).'.'.$file->extension();
        $file->move(public_path('assets/'.$folder), $nama);

        return $folder.'/'.$nama;
    }
}
