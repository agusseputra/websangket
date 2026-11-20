<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BeritaRequest;
use App\Http\Resources\BeritaResource;
use App\Models\Article;
use App\Services\GambarBeritaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BeritaController extends Controller
{
    public const PER_HALAMAN = 20;

    public function __construct(private GambarBeritaService $gambar) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kategori_id' => ['nullable', 'integer'],
        ]);

        $berita = Article::with('category')
            ->when($filter['q'] ?? null, fn ($query, string $q) => $query->where('title', 'like', '%'.addcslashes(trim($q), '%_\\').'%'))
            ->when($filter['kategori_id'] ?? null, fn ($query, int $id) => $query->where('category_id', $id))
            ->latest()
            ->latest('id')
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        return BeritaResource::collection($berita);
    }

    public function show(Article $berita): BeritaResource
    {
        return new BeritaResource($berita->load('category'));
    }

    public function store(BeritaRequest $request): JsonResponse
    {
        $berita = Article::create($this->atribut($request));

        return (new BeritaResource($berita->load('category')))->response()->setStatusCode(201);
    }

    public function update(BeritaRequest $request, Article $berita): BeritaResource
    {
        $sampulLama = $berita->image;
        $atribut = $this->atribut($request, $berita);
        $berita->update($atribut);

        if (array_key_exists('image', $atribut) && $sampulLama !== $atribut['image']) {
            $this->gambar->hapusSampul($sampulLama);
        }

        return new BeritaResource($berita->load('category'));
    }

    public function destroy(Article $berita): JsonResponse
    {
        $sampul = $berita->image;
        $berita->delete();
        $this->gambar->hapusSampul($sampul);

        return response()->json(status: 204);
    }

    /**
     * Mengunggah gambar dari tombol gambar di editor dan mengembalikan URL-nya.
     */
    public function unggahGambar(Request $request): JsonResponse
    {
        $request->validate([
            'gambar' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:'.BeritaRequest::UKURAN_GAMBAR_KB],
        ], [
            'gambar.required' => 'Pilih gambar yang akan diunggah.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus JPG, PNG, WebP, atau GIF.',
            'gambar.max' => 'Ukuran gambar maksimal 1 MB.',
            'gambar.uploaded' => 'Gambar gagal diunggah. Ukuran maksimal 1 MB.',
        ]);

        return response()->json(['url' => $this->gambar->simpanKonten($request->file('gambar'))], 201);
    }

    /**
     * @return array<string, mixed>
     */
    private function atribut(BeritaRequest $request, ?Article $berita = null): array
    {
        $data = $request->validated();
        $atribut = [
            'title' => $data['judul'],
            'category_id' => $data['kategori_id'],
            'content' => $this->gambar->pindahkanBase64($data['isi']),
        ];

        if ($request->hasFile('sampul')) {
            $atribut['image'] = $this->gambar->simpanSampul($request->file('sampul'));
        } elseif ($berita && $data['hapus_sampul']) {
            $atribut['image'] = null;
        }

        return $atribut;
    }
}
