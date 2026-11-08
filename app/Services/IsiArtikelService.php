<?php

namespace App\Services;

use Dom\Element;
use Dom\HTMLDocument;

/**
 * Merapikan HTML isi artikel dari editor sebelum ditampilkan di halaman publik.
 */
class IsiArtikelService
{
    private const TAG_DIBUANG = 'script, style, iframe, object, embed, form, input, button, textarea, select, link, meta, base';

    public function bersihkan(string $html): string
    {
        $dokumen = HTMLDocument::createFromString('<!DOCTYPE html><body><div id="akar">'.$html.'</div></body>', LIBXML_NOERROR, 'UTF-8');
        $akar = $dokumen->getElementById('akar');

        foreach ($akar->querySelectorAll(self::TAG_DIBUANG) as $elemen) {
            $elemen->remove();
        }

        foreach ($akar->querySelectorAll('*') as $elemen) {
            $this->bersihkanAtribut($elemen);
        }

        foreach ($akar->querySelectorAll('img') as $gambar) {
            $src = trim($gambar->getAttribute('src') ?? '');

            // Gambar rusak dari editor lama (src "//:0") dan gambar tanpa sumber tidak ditampilkan.
            if ($src === '' || str_starts_with($src, '//:')) {
                $gambar->remove();

                continue;
            }

            $gambar->setAttribute('loading', 'lazy');
            $gambar->setAttribute('decoding', 'async');

            if (! $gambar->hasAttribute('alt')) {
                $gambar->setAttribute('alt', '');
            }
        }

        // Halaman hanya boleh punya satu h1, yaitu judul artikel.
        foreach ($akar->querySelectorAll('h1') as $judul) {
            $pengganti = $dokumen->createElement('h2');
            $pengganti->append(...$judul->childNodes);
            $judul->replaceWith($pengganti);
        }

        foreach ($akar->querySelectorAll('a[href]') as $tautan) {
            if (preg_match('#^https?://#i', $tautan->getAttribute('href')) && ! str_starts_with($tautan->getAttribute('href'), url('/'))) {
                $tautan->setAttribute('target', '_blank');
                $tautan->setAttribute('rel', 'noopener noreferrer');
            }
        }

        // Paragraf yang kosong setelah gambar rusak dibuang tidak perlu memakan ruang.
        foreach ($akar->querySelectorAll('p') as $paragraf) {
            if (trim(str_replace("\u{00A0}", '', $paragraf->textContent)) === '' && $paragraf->querySelector('img') === null) {
                $paragraf->remove();
            }
        }

        return trim($akar->innerHTML);
    }

    private function bersihkanAtribut(Element $elemen): void
    {
        foreach (iterator_to_array($elemen->attributes) as $atribut) {
            $nama = strtolower($atribut->name);
            $nilai = strtolower(preg_replace('/\s+/', '', $atribut->value));

            if (str_starts_with($nama, 'on') || $nama === 'style' && str_contains($nilai, 'expression(')) {
                $elemen->removeAttribute($atribut->name);

                continue;
            }

            if (in_array($nama, ['href', 'src', 'xlink:href', 'action', 'formaction'], true)
                && (str_starts_with($nilai, 'javascript:') || str_starts_with($nilai, 'vbscript:') || str_starts_with($nilai, 'data:text'))) {
                $elemen->removeAttribute($atribut->name);
            }
        }
    }
}
