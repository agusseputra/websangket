<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalamanPublikTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_memuat_meta_description_dan_open_graph(): void
    {
        config(['app.url' => 'https://dudukan.contoh.test']);

        $respons = $this->get('/')->assertOk();

        $respons->assertSee('<title>Desa Adat Sangket</title>', false)
            ->assertSee('<meta name="description" content="Desa Adat Sangket, Sukasada, Buleleng, Bali. Melalui program prioritas Kasukretan Krama', false)
            ->assertSee('<meta property="og:title" content="Desa Adat Sangket">', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta property="og:locale" content="id_ID">', false)
            ->assertSee('<meta property="og:image:width" content="1200">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertDontSee('noindex', false);

        $this->assertMatchesRegularExpression('#<meta property="og:image" content="https?://[^"]+/images/og-dudukan-sangket\.jpg">#', $respons->getContent());
        $this->assertFileExists(public_path('images/og-dudukan-sangket.jpg'));
    }

    public function test_halaman_aplikasi_tidak_diindeks_dan_tanpa_open_graph(): void
    {
        foreach (['/login', '/admin', '/krama'] as $alamat) {
            $this->get($alamat)
                ->assertOk()
                ->assertSee('<meta name="robots" content="noindex, nofollow">', false)
                ->assertDontSee('og:title', false);
        }
    }

    public function test_landing_dan_halaman_berita_memakai_navbar_yang_sama(): void
    {
        $this->withoutVite();

        foreach (['/', '/listarticle', '/profil', '/lpd'] as $alamat) {
            $this->get($alamat)
                ->assertOk()
                ->assertSeeInOrder(['id="navbar-publik"', 'href="'.url('/profil').'"', 'Profil', 'href="'.url('/#kasukretan-krama').'"', 'Kasukretan Krama', 'href="'.url('/listarticle').'"', 'Berita', 'href="'.url('/lpd').'"', 'LPD', 'Cek pembayaran', 'Pelaporan'], false);
        }

        $this->get('/login')->assertOk()->assertDontSee('navbar-publik', false);
    }
}
