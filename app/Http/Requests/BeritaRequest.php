<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BeritaRequest extends FormRequest
{
    public const UKURAN_GAMBAR_KB = 1024;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'judul' => Str::squish((string) $this->input('judul')),
            'hapus_sampul' => $this->boolean('hapus_sampul'),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $berita = $this->route('berita');

        return [
            'judul' => ['required', 'string', 'min:10', 'max:200', Rule::unique('articles', 'title')->ignore($berita)],
            'kategori_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'isi' => [
                'required', 'string', 'max:5000000',
                function (string $attribute, mixed $value, Closure $fail) {
                    $teks = trim(str_replace("\u{00A0}", ' ', html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

                    if (mb_strlen($teks) < 30) {
                        $fail('Isi berita minimal 30 karakter teks.');
                    }

                    // Gambar yang ditempel langsung ke editor juga dibatasi seperti gambar yang diunggah.
                    preg_match_all('#data:image/[a-z]+;base64,([A-Za-z0-9+/=\s]+)#i', (string) $value, $cocok);

                    foreach ($cocok[1] as $base64) {
                        if (strlen(preg_replace('/\s+/', '', $base64)) * 3 / 4 > self::UKURAN_GAMBAR_KB * 1024) {
                            $fail('Gambar di dalam isi berita maksimal 1 MB per gambar.');

                            return;
                        }
                    }
                },
            ],
            'sampul' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::UKURAN_GAMBAR_KB, 'dimensions:min_width=400,min_height=200'],
            'hapus_sampul' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul berita wajib diisi.',
            'judul.min' => 'Judul berita minimal 10 karakter.',
            'judul.max' => 'Judul berita maksimal 200 karakter.',
            'judul.unique' => 'Judul ini sudah dipakai berita lain.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak ditemukan.',
            'isi.required' => 'Isi berita wajib diisi.',
            'isi.max' => 'Isi berita terlalu besar. Kecilkan atau kurangi gambar di dalamnya.',
            'sampul.image' => 'Foto sampul harus berupa gambar.',
            'sampul.mimes' => 'Foto sampul harus JPG, PNG, atau WebP.',
            'sampul.max' => 'Ukuran foto sampul maksimal 1 MB.',
            'sampul.dimensions' => 'Foto sampul minimal 400 × 200 piksel.',
            'sampul.uploaded' => 'Foto sampul gagal diunggah. Ukuran maksimal 1 MB.',
        ];
    }
}
