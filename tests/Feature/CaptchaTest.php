<?php

namespace Tests\Feature;

use App\Services\CaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Lottery;
use Illuminate\Support\Str;
use Tests\TestCase;

class CaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_captcha_memberi_kunci_dan_gambar_png_tanpa_disimpan_browser(): void
    {
        $respons = $this->getJson('/api/captcha')
            ->assertOk()
            ->assertJsonPath('berlaku_detik', 600)
            ->assertHeader('Cache-Control', 'no-store, private');

        $this->assertTrue(Str::isUuid($respons->json('kunci')));
        $this->assertStringStartsWith('data:image/png;base64,', $respons->json('gambar'));

        $ukuran = getimagesizefromstring(base64_decode(Str::after($respons->json('gambar'), ',')));
        $this->assertSame([200, 64], [$ukuran[0], $ukuran[1]]);
    }

    public function test_permintaan_captcha_dibatasi_per_menit(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->getJson('/api/captcha')->assertOk();
        }

        $this->getJson('/api/captcha')->assertTooManyRequests();
    }

    public function test_captcha_kedaluwarsa_dibersihkan_dari_cache_database_tanpa_menyentuh_cache_lain(): void
    {
        config(['cache.default' => 'database']);
        $captcha = app(CaptchaService::class);

        Lottery::alwaysLose(function () use ($captcha) {
            $captcha->buat();
            $captcha->buat();
        });
        Cache::put('cache-lain', 'tetap', now()->addMinutes(30));

        $this->travel(CaptchaService::BERLAKU_MENIT + 1)->minutes();

        $baru = null;
        Lottery::alwaysWin(function () use ($captcha, &$baru) {
            $baru = $captcha->buat();
        });

        $this->assertSame(2, DB::table('cache')->count());
        $this->assertTrue(Cache::has('captcha:'.$baru['kunci']));
        $this->assertTrue(DB::table('cache')->where('key', 'like', '%cache-lain')->exists());
    }
}
