<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Services\IsiArtikelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Halaman artikel publik dengan URL yang sama seperti web lama sangketdesa.com.
 */
class ArtikelPublikController extends Controller
{
    private const PER_HALAMAN = 9;

    public function daftar(Request $request, ?string $kategori = null): View
    {
        // Kata kunci dibatasi 100 karakter; kata kunci panjang dipotong, bukan ditolak.
        $kataKunci = mb_substr(trim(is_string($request->query('q')) ? $request->query('q') : ''), 0, 100);
        $kategoriAktif = null;

        if ($kategori !== null) {
            $kategoriAktif = Category::cariSlug($kategori) ?? abort(404);
        }

        $artikel = Article::query()
            ->with('category')
            ->when($kategoriAktif, fn ($query) => $query->whereBelongsTo($kategoriAktif))
            ->when($kataKunci !== '', fn ($query) => $query->where(function ($query) use ($kataKunci) {
                foreach (preg_split('/\s+/', $kataKunci) as $kata) {
                    $pola = '%'.addcslashes($kata, '%_\\').'%';
                    $query->where(fn ($satuKata) => $satuKata->where('title', 'like', $pola)->orWhere('content', 'like', $pola));
                }
            }))
            ->latest()
            ->latest('id')
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        if ($artikel->currentPage() > max(1, $artikel->lastPage())) {
            abort(404);
        }

        return view('artikel.daftar', [
            'kategoriAktif' => $kategoriAktif,
            'kataKunci' => $kataKunci,
            'artikel' => $artikel,
            'semuaKategori' => $this->semuaKategori(),
        ]);
    }

    public function detail(IsiArtikelService $isi, int $id, ?string $slug = null): View|RedirectResponse
    {
        $artikel = Article::with('category')->findOrFail($id);

        // Slug lama yang berbeda (misalnya judul pernah diubah) diarahkan permanen ke alamat yang benar.
        if ($slug !== $artikel->slug) {
            return redirect()->to($artikel->url(), 301);
        }

        $berikutnya = fn (string $arah) => Article::query()
            ->where('created_at', $arah === 'lama' ? '<' : '>', $artikel->created_at)
            ->orderBy('created_at', $arah === 'lama' ? 'desc' : 'asc')
            ->first(['id', 'title', 'created_at']);

        return view('artikel.detail', [
            'artikel' => $artikel,
            'isi' => $isi->bersihkan($artikel->content),
            'sebelumnya' => $berikutnya('lama'),
            'selanjutnya' => $berikutnya('baru'),
            'terkait' => Article::with('category')->whereBelongsTo($artikel->category)->whereKeyNot($artikel->id)->latest()->limit(3)->get(),
        ]);
    }

    public function sitemap(): Response
    {
        return response()
            ->view('artikel.sitemap', [
                'artikel' => Article::query()->latest()->get(['id', 'title', 'updated_at', 'created_at']),
                'semuaKategori' => Category::query()->has('articles')->orderBy('id')->get(),
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @return Collection<int, Category>
     */
    private function semuaKategori(): Collection
    {
        return Category::query()->withCount('articles')->orderBy('id')->get();
    }
}
