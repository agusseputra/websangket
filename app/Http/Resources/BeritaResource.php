<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Article
 */
class BeritaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->title,
            'kategori_id' => $this->category_id,
            'kategori' => $this->category?->name,
            'ringkasan' => $this->ringkasan(120),
            'sampul' => $this->gambarUrl(),
            'url' => $this->url(),
            'isi' => $this->when($request->routeIs('berita.show'), fn () => $this->content),
            'dibuat' => $this->created_at?->toIso8601String(),
            'diubah' => $this->updated_at?->toIso8601String(),
        ];
    }
}
