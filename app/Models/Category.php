<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Slug huruf kecil seperti di web lama, misalnya "Lembaga Adat" menjadi "lembaga-adat".
     */
    protected function slug(): Attribute
    {
        return Attribute::get(fn (): string => Str::slug($this->name));
    }

    public function url(): string
    {
        return route('artikel.daftar', $this->slug);
    }

    public static function cariSlug(string $slug): ?self
    {
        return static::query()->get()->first(fn (self $kategori) => $kategori->slug === Str::lower($slug));
    }
}
