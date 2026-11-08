<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class ValidRecaptcha implements ValidationRule
{
    private const URL_VERIFIKASI = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Tanpa kunci rahasia hanya lingkungan lokal yang lolos, jadi produksi tetap menolak jika belum dikonfigurasi.
     */
    public static function dibutuhkan(): bool
    {
        return filled(config('services.recaptcha.secret')) || ! app()->environment('local');
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rahasia = config('services.recaptcha.secret');

        if (blank($rahasia)) {
            $fail('Verifikasi CAPTCHA belum dikonfigurasi. Hubungi tim Kasukretan.');

            return;
        }

        try {
            $berhasil = Http::asForm()->timeout(10)->post(self::URL_VERIFIKASI, [
                'secret' => $rahasia,
                'response' => $value,
                'remoteip' => request()->ip(),
            ])->json('success') === true;
        } catch (ConnectionException) {
            $berhasil = false;
        }

        if (! $berhasil) {
            $fail('Verifikasi CAPTCHA gagal. Centang ulang lalu kirim kembali.');
        }
    }
}
