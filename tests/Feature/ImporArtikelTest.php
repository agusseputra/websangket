<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ImporArtikelTest extends TestCase
{
    use RefreshDatabase;

    private const KATEGORI_SQL = <<<'SQL'
        CREATE TABLE `categories` (`id` bigint(20) UNSIGNED NOT NULL) ENGINE=InnoDB;
        INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
        (3, 'Perarem', '2025-03-24 17:15:52', '2025-03-26 21:46:12'),
        (6, 'Berita', '2025-05-23 04:37:56', '2025-05-23 04:37:56');
        COMMIT;
        SQL;

    private const ARTIKEL_SQL = <<<'SQL'
        INSERT INTO `articles` (`id`, `title`, `content`, `image`, `category_id`, `created_at`, `updated_at`) VALUES
        (14, 'Paruman Desa, Merakih (2025)', '<p class=\"ql-align-justify\">Krama\'s rapat;</p>\r\n<p>O''Neil \\ selesai</p>', 'article/a.jpg', 6, '2025-06-09 07:05:53', '2025-06-09 07:05:53');
        INSERT INTO `articles` (`id`, `title`, `content`, `image`, `category_id`, `created_at`, `updated_at`) VALUES
        (27, 'Dudonan Piodalan', '<p>Isi</p>', NULL, 3, NULL, NULL);
        ALTER TABLE `articles` ADD PRIMARY KEY (`id`);
        SQL;

    private function tulisDump(string $isi): string
    {
        $path = tempnam(sys_get_temp_dir(), 'dump');
        file_put_contents($path, $isi);

        return $path;
    }

    public function test_impor_menyalin_id_isi_dan_relasi_dari_dump(): void
    {
        $kategori = $this->tulisDump(self::KATEGORI_SQL);
        $artikel = $this->tulisDump(self::ARTIKEL_SQL);

        $this->artisan('artikel:impor', ['kategori' => $kategori, 'artikel' => $artikel])
            ->expectsOutput('2 kategori dan 2 artikel diimpor.')
            ->assertSuccessful();

        $paruman = Article::findOrFail(14);
        $this->assertSame('Paruman Desa, Merakih (2025)', $paruman->title);
        $this->assertSame("<p class=\"ql-align-justify\">Krama's rapat;</p>\r\n<p>O'Neil \\ selesai</p>", $paruman->content);
        $this->assertSame('article/a.jpg', $paruman->image);
        $this->assertSame('Berita', $paruman->category->name);
        // Dump tercatat dalam UTC, aplikasi memakai WITA (UTC+8).
        $this->assertSame('2025-06-09 15:05:53', $paruman->created_at->format('Y-m-d H:i:s'));
        $this->assertNull(Article::findOrFail(27)->image);
        $this->assertSame([3, 6], Category::orderBy('id')->pluck('id')->all());

        // Impor ulang memperbarui baris yang sama tanpa duplikasi.
        Article::whereKey(27)->update(['title' => 'Diubah']);
        $this->artisan('artikel:impor', ['kategori' => $kategori, 'artikel' => $artikel])->assertSuccessful();

        $this->assertSame(2, Article::count());
        $this->assertSame('Dudonan Piodalan', Article::findOrFail(27)->title);
    }

    public function test_impor_dibatalkan_jika_artikel_memakai_kategori_yang_tidak_ada(): void
    {
        $kategori = $this->tulisDump(str_replace("(3, 'Perarem', '2025-03-24 17:15:52', '2025-03-26 21:46:12'),\n", '', self::KATEGORI_SQL));
        $artikel = $this->tulisDump(self::ARTIKEL_SQL);

        $this->artisan('artikel:impor', ['kategori' => $kategori, 'artikel' => $artikel])
            ->expectsOutput('Artikel id 27 memakai kategori yang tidak ada di dump kategori.')
            ->assertFailed();

        $this->assertSame(0, Category::count());
        $this->assertSame(0, Article::count());
    }

    public function test_impor_gagal_jika_dump_bukan_tabel_yang_diminta(): void
    {
        $artikel = $this->tulisDump(self::ARTIKEL_SQL);

        $this->artisan('artikel:impor', ['kategori' => $artikel, 'artikel' => $artikel])
            ->expectsOutput("Perintah INSERT untuk tabel categories tidak ditemukan di {$artikel}.")
            ->assertFailed();

        $this->artisan('artikel:impor', ['kategori' => '/tidak/ada.sql', 'artikel' => $artikel])
            ->expectsOutput('File /tidak/ada.sql tidak ditemukan atau tidak bisa dibaca.')
            ->assertFailed();
    }

    public function test_ekstrak_gambar_memindahkan_base64_ke_file_dan_mengganti_src(): void
    {
        $folderPublik = sys_get_temp_dir().'/publik-uji-'.uniqid();
        $this->app->usePublicPath($folderPublik);
        $artikel = Article::factory()->create(['content' => '<p><img src="data:image/png;base64,'.base64_encode('isi-png').'"></p><p><img src="/lain.jpg"></p>']);

        $this->artisan('artikel:ekstrak-gambar')->assertSuccessful();

        $this->assertSame('<p><img src="/assets/article/konten/'.$artikel->id.'-1.png"></p><p><img src="/lain.jpg"></p>', $artikel->fresh()->content);
        $this->assertSame('isi-png', file_get_contents($folderPublik.'/assets/article/konten/'.$artikel->id.'-1.png'));

        File::deleteDirectory($folderPublik);
    }
}
