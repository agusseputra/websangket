<?php

namespace App\View\Components;

use App\Models\Article;
use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * Sidebar halaman publik: pencarian, kategori, artikel terbaru, layanan krama, dan profil desa adat.
 */
class SidebarPublik extends Component
{
    /** @var Collection<int, Category> */
    public Collection $semuaKategori;

    /** @var Collection<int, Article> */
    public Collection $terbaru;

    public function __construct(
        public string $label = 'Navigasi halaman',
        public ?Category $kategoriAktif = null,
        public ?int $kecualiArtikel = null,
    ) {
        $this->semuaKategori = Category::query()->withCount('articles')->orderBy('id')->get();
        $this->terbaru = Article::query()
            ->when($kecualiArtikel, fn ($query) => $query->whereKeyNot($kecualiArtikel))
            ->latest()
            ->latest('id')
            ->limit(5)
            ->get(['id', 'title', 'image', 'created_at']);
    }

    public function render(): View|Closure|string
    {
        return view('components.sidebar-publik');
    }
}
