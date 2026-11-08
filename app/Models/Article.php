<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['title', 'content', 'image', 'category_id'])]
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    public const PANJANG_RINGKASAN = 225;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Slug mengikuti web lama: spasi menjadi tanda hubung, huruf besar dan tanda baca dipertahankan.
     */
    protected function slug(): Attribute
    {
        return Attribute::get(fn (): string => trim(preg_replace(['#[/?\#%\\\\]+#', '/\s+/u'], ['', '-'], trim($this->title)), '-'));
    }

    public function url(): string
    {
        return route('artikel.detail', ['id' => $this->id, 'slug' => $this->slug]);
    }

    /**
     * Teks polos dari isi artikel, tanpa gambar base64 dan tag HTML.
     */
    public function teksPolos(): string
    {
        $tanpaGambar = preg_replace(['#<(script|style)\b[^>]*>.*?</\1>#is', '#<img\b[^>]*>#i'], ' ', $this->content);
        $berjarak = preg_replace('#</(p|div|li|h[1-6]|br)>|<br\s*/?>#i', ' ', $tanpaGambar);
        $teks = html_entity_decode(strip_tags($berjarak), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $teks));
    }

    public function ringkasan(int $panjang = self::PANJANG_RINGKASAN): string
    {
        return Str::limit($this->teksPolos(), $panjang, '…', preserveWords: true);
    }

    public function menitBaca(): int
    {
        return max(1, (int) ceil(str_word_count($this->teksPolos()) / 200));
    }

    public function tanggalTampil(): string
    {
        return $this->created_at?->locale('id')->translatedFormat('j F Y') ?? '';
    }

    /**
     * URL foto sampul di /assets seperti web lama, atau null jika filenya belum tersedia.
     */
    public function gambarUrl(): ?string
    {
        if (blank($this->image) || ! is_file(public_path('assets/'.$this->image))) {
            return null;
        }

        return asset('assets/'.$this->image);
    }
}
