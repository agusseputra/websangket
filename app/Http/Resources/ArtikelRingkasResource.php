<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Article
 */
class ArtikelRingkasResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->title,
            'url' => $this->url(),
            'ringkasan' => $this->ringkasan(140),
            'gambar' => $this->gambarUrl(),
            'kategori' => $this->category->name,
            'kategori_url' => $this->category->url(),
            'tanggal' => $this->created_at?->toIso8601String(),
            'tanggal_tampil' => $this->created_at?->locale('id')->translatedFormat('j F Y'),
        ];
    }
}
