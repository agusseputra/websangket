<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ArtikelPublikTest extends TestCase
{
    use RefreshDatabase;

    private string $folderPublik;

    protected function setUp(): void
    {
        parent::setUp();

        // Folder publik sementara agar uji gambar sampul tidak menyentuh public/ proyek.
        $this->folderPublik = sys_get_temp_dir().'/publik-uji-'.uniqid();
        File::ensureDirectoryExists($this->folderPublik.'/assets/article');
        $this->app->usePublicPath($this->folderPublik);
        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->folderPublik);

        parent::tearDown();
    }

    private function artikel(array $atribut = []): Article
    {
        return Article::factory()->for(Category::factory()->create(['name' => 'Berita']))->create($atribut);
    }

    public function test_detail_artikel_dengan_url_lama_memuat_seo_dinamis(): void
    {
        File::put($this->folderPublik.'/assets/article/sampul.jpg', 'gambar');
        $kalimat = str_repeat('Krama ngayah bersama di pura desa. ', 12);
        $artikel = $this->artikel([
            'title' => 'Meriah dan Inspiratif, Penutupan Pesraman',
            'content' => '<p><img src="data:image/png;base64,QUJD"></p><p>'.$kalimat.'&nbsp;Penutup.</p>',
            'image' => 'article/sampul.jpg',
            'created_at' => '2026-04-06 14:31:55',
        ]);
        $url = url('/detailarticle/'.$artikel->id.'/Meriah-dan-Inspiratif,-Penutupan-Pesraman');

        $respons = $this->get($url)->assertOk();
        $html = $respons->getContent();
        preg_match('/<meta name="description" content="([^"]*)">/', $html, $deskripsi);
        $deskripsi = html_entity_decode($deskripsi[1]);

        $respons->assertSee('<title>Meriah dan Inspiratif, Penutupan Pesraman | Desa Adat Sangket</title>', false)
            ->assertSee('<link rel="canonical" href="'.$url.'">', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:title" content="Meriah dan Inspiratif, Penutupan Pesraman">', false)
            ->assertSee('<meta property="og:image" content="'.url('/assets/article/sampul.jpg').'">', false)
            ->assertSee('<meta property="article:published_time" content="2026-04-06T14:31:55+08:00">', false)
            ->assertSee('<meta property="article:section" content="Berita">', false)
            ->assertSee('<link rel="icon" type="image/png" href="/images/logo-desa-adat-sangket.png">', false)
            ->assertSee('"@type":"NewsArticle"', false)
            ->assertSee('<time datetime="2026-04-06T14:31:55+08:00">6 April 2026</time>', false);

        $this->assertStringStartsWith('Krama ngayah bersama di pura desa. Krama', $deskripsi);
        $this->assertStringEndsWith('…', $deskripsi);
        $this->assertLessThanOrEqual(226, mb_strlen($deskripsi));
        $this->assertStringNotContainsString('base64', $deskripsi);
        $this->assertSame(1, substr_count($html, '<h1'));
    }

    public function test_artikel_tanpa_foto_memakai_gambar_og_desa(): void
    {
        $artikel = $this->artikel(['image' => 'article/belum-disalin.png']);

        $this->get($artikel->url())
            ->assertOk()
            ->assertSee('<meta property="og:image" content="'.url('/images/og-dudukan-sangket.jpg').'">', false);
    }

    public function test_isi_artikel_dibersihkan_sebelum_ditampilkan(): void
    {
        $artikel = $this->artikel([
            'content' => '<h1>Subjudul</h1><p class="ql-align-justify">Isi <a href="javascript:alert(1)">tautan</a></p>'
                .'<p><img src="//:0"></p><p><img src="/foto.png" onerror="alert(1)"></p><script>alert(1)</script>',
        ]);

        $html = $this->get($artikel->url())->assertOk()->getContent();

        $this->assertStringContainsString('<h2>Subjudul</h2>', $html);
        $this->assertStringContainsString('<p class="ql-align-justify">Isi <a>tautan</a></p>', $html);
        $this->assertStringContainsString('<img src="/foto.png" loading="lazy" decoding="async" alt="">', $html);
        $this->assertStringNotContainsString('//:0', $html);
        $this->assertStringNotContainsString('alert(1)', $html);
    }

    public function test_slug_salah_atau_kosong_dialihkan_permanen_ke_url_benar(): void
    {
        $artikel = $this->artikel(['title' => 'Laporan Dudukan 2026']);

        $this->get('/detailarticle/'.$artikel->id.'/judul-lama')->assertRedirect($artikel->url())->assertStatus(301);
        $this->get('/detailarticle/'.$artikel->id)->assertRedirect($artikel->url())->assertStatus(301);
    }

    public function test_artikel_yang_tidak_ada_menampilkan_404(): void
    {
        $this->get('/detailarticle/999/Tidak-Ada')
            ->assertNotFound()
            ->assertSee('Halaman tidak ditemukan')
            ->assertSee('<meta name="robots" content="noindex, follow">', false);

        $this->get('/detailarticle/abc/Tidak-Ada')->assertNotFound();
    }

    public function test_daftar_per_kategori_memakai_slug_lama_dan_hanya_menampilkan_kategori_itu(): void
    {
        $lembaga = Category::factory()->create(['name' => 'Lembaga Adat']);
        Article::factory()->for($lembaga)->create(['title' => 'Prajuru Desa Adat']);
        $this->artikel(['title' => 'Paruman Desa']);

        $this->get('/listarticle/lembaga-adat')
            ->assertOk()
            ->assertSee('<title>Lembaga Adat | Desa Adat Sangket</title>', false)
            ->assertSee('<link rel="canonical" href="'.url('/listarticle/lembaga-adat').'">', false)
            ->assertSee('Prajuru Desa Adat')
            ->assertDontSee('Paruman Desa');

        $this->get('/listarticle')->assertOk()->assertSee('Prajuru Desa Adat')->assertSee('Paruman Desa');
        $this->get('/listarticle/tidak-ada')->assertNotFound();
    }

    public function test_daftar_artikel_dibagi_per_halaman(): void
    {
        $berita = Category::factory()->create(['name' => 'Berita']);
        Article::factory()->for($berita)->create(['title' => 'Artikel Paling Lama', 'created_at' => '2025-01-01 08:00:00']);
        Article::factory()->for($berita)->count(9)->create(['created_at' => '2026-01-01 08:00:00']);

        $this->get('/listarticle/berita')
            ->assertOk()
            ->assertDontSee('Artikel Paling Lama')
            ->assertSee('<link rel="next" href="'.url('/listarticle/berita?page=2').'">', false);

        $this->get('/listarticle/berita?page=2')
            ->assertOk()
            ->assertSee('Artikel Paling Lama')
            ->assertSee('<title>Berita – Halaman 2 | Desa Adat Sangket</title>', false);

        $this->get('/listarticle/berita?page=3')->assertNotFound();
    }

    public function test_sitemap_memuat_daftar_kategori_dan_artikel(): void
    {
        $artikel = $this->artikel();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.e($artikel->url()).'</loc>', false)
            ->assertSee('<loc>'.url('/listarticle/berita').'</loc>', false);
    }

    public function test_api_artikel_terbaru_mengembalikan_tiga_artikel_terbaru(): void
    {
        $berita = Category::factory()->create(['name' => 'Berita']);
        Article::factory()->for($berita)->create(['title' => 'Lama', 'created_at' => '2025-01-01 08:00:00']);
        Article::factory()->for($berita)->count(3)->create(['created_at' => '2026-01-01 08:00:00']);

        $this->getJson('/api/artikel-terbaru')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonMissing(['judul' => 'Lama'])
            ->assertJsonPath('data.0.kategori_url', url('/listarticle/berita'));
    }

    public function test_pencarian_berita_mencocokkan_judul_dan_isi_di_dalam_kategori(): void
    {
        $berita = Category::factory()->create(['name' => 'Berita']);
        $pengumuman = Category::factory()->create(['name' => 'Pengumuman']);
        Article::factory()->for($berita)->create(['title' => 'Piodalan Pura Desa', 'content' => '<p>Upacara di pura.</p>']);
        Article::factory()->for($berita)->create(['title' => 'Paruman Krama', 'content' => '<p>Membahas jadwal piodalan berikutnya.</p>']);
        Article::factory()->for($berita)->create(['title' => 'Gotong Royong', 'content' => '<p>Bersih lingkungan.</p>']);
        Article::factory()->for($pengumuman)->create(['title' => 'Piodalan Pura Dalem', 'content' => '<p>Pengumuman.</p>']);

        $this->get('/listarticle?q=piodalan')
            ->assertOk()
            ->assertSee('Piodalan Pura Desa')
            ->assertSee('Paruman Krama')
            ->assertSee('Piodalan Pura Dalem')
            ->assertDontSee('Gotong Royong')
            ->assertSee('<meta name="robots" content="noindex, follow">', false)
            ->assertSee('value="piodalan"', false);

        $this->get('/listarticle/berita?q=piodalan pura')
            ->assertOk()
            ->assertSee('Piodalan Pura Desa')
            ->assertDontSee('Paruman Krama')
            ->assertDontSee('Piodalan Pura Dalem');

        $this->get('/listarticle?q=tidakadakatainidimanapun')
            ->assertOk()
            ->assertSee('Berita tidak ditemukan');
    }

    public function test_pencarian_tetap_dibawa_saat_pindah_halaman(): void
    {
        $berita = Category::factory()->create(['name' => 'Berita']);
        Article::factory()->for($berita)->count(10)->create(['content' => '<p>Kabar piodalan.</p>']);

        $this->get('/listarticle?q=piodalan')
            ->assertOk()
            ->assertSee('<link rel="next" href="'.url('/listarticle?q=piodalan&amp;page=2').'">', false);
    }

    public function test_halaman_profil_memuat_seo_dan_sidebar_bersama(): void
    {
        $artikel = $this->artikel(['title' => 'Paruman Desa Adat']);

        $respons = $this->get('/profil')
            ->assertOk()
            ->assertSee('<title>Profil Desa Adat Sangket – Sejarah, Prajuru, dan Lembaga Adat</title>', false)
            ->assertSee('<link rel="canonical" href="'.url('/profil').'">', false)
            ->assertSee('"@type":"AboutPage"', false)
            ->assertSeeInOrder(['Kertha Desa', 'Sabha Desa', 'Pecalang', 'Sekeha Gong', 'Yowana', 'LPD Desa Adat Sangket'])
            ->assertSeeInOrder(['Parahyangan', 'Pawongan', 'Palemahan'])
            ->assertSee('aria-label="Navigasi profil"', false)
            ->assertSee($artikel->url(), false);

        $this->assertSame(1, substr_count($respons->getContent(), '<h1'));
        $this->get('/sitemap.xml')->assertSee('<loc>'.url('/profil').'</loc>', false);
    }

    public function test_halaman_lpd_memuat_seo_dan_bagian_utama(): void
    {
        $respons = $this->get('/lpd')
            ->assertOk()
            ->assertSee('<title>LPD Desa Adat Sangket – Profil, Prajuru, dan Layanan</title>', false)
            ->assertSee('<link rel="canonical" href="'.url('/lpd').'">', false)
            ->assertSee('"@type":"FinancialService"', false)
            ->assertSeeInOrder(['id="profil-lpd"', 'id="prajuru-lpd"', 'id="program-lpd"'], false)
            ->assertSee('aria-label="Navigasi LPD"', false);

        $this->assertSame(1, substr_count($respons->getContent(), '<h1'));
        $this->get('/profil')->assertSee('href="'.url('/lpd').'"', false);
        $this->get('/sitemap.xml')->assertSee('<loc>'.url('/lpd').'</loc>', false);
    }
}
