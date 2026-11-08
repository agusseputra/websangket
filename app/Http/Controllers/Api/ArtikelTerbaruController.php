<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtikelRingkasResource;
use App\Models\Article;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArtikelTerbaruController extends Controller
{
    /**
     * Tiga artikel terbaru untuk bagian Kabar desa di landing page.
     */
    public function __invoke(): AnonymousResourceCollection
    {
        return ArtikelRingkasResource::collection(Article::with('category')->latest()->latest('id')->limit(3)->get());
    }
}
