<?php

namespace Tests\Feature;

use App\Rules\ValidRecaptcha;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * reCAPTCHA Google sedang tidak dipakai form laporan, tetapi aturannya disimpan untuk dipakai lagi.
 */
class ValidRecaptchaTest extends TestCase
{
    private function lolos(string $token = 'token-uji'): bool
    {
        return Validator::make(['captcha' => $token], ['captcha' => [new ValidRecaptcha]])->passes();
    }

    public function test_token_diterima_hanya_jika_google_menyatakan_berhasil(): void
    {
        config(['services.recaptcha.secret' => 'rahasia-uji']);
        Http::fake(['www.google.com/recaptcha/*' => Http::sequence()->push(['success' => true])->push(['success' => false])]);

        $this->assertTrue($this->lolos());
        $this->assertFalse($this->lolos());
        Http::assertSent(fn ($permintaan) => $permintaan['secret'] === 'rahasia-uji' && $permintaan['response'] === 'token-uji');
    }

    public function test_ditolak_jika_google_tidak_bisa_dihubungi_atau_kunci_kosong(): void
    {
        config(['services.recaptcha.secret' => 'rahasia-uji']);
        Http::fake(['www.google.com/recaptcha/*' => fn () => throw new ConnectionException('Tidak terhubung')]);
        $this->assertFalse($this->lolos());

        config(['services.recaptcha.secret' => null]);
        $this->assertFalse($this->lolos());

        $this->app['env'] = 'production';
        $this->assertTrue(ValidRecaptcha::dibutuhkan());
        $this->app['env'] = 'local';
        $this->assertFalse(ValidRecaptcha::dibutuhkan());
    }
}
