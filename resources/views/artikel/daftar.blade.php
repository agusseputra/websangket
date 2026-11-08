@php
    $nama = $kategoriAktif?->name ?? 'Semua Berita';
    $halaman = $artikel->currentPage();
    $mencari = $kataKunci !== '';
    $canonical = $kategoriAktif ? $kategoriAktif->url() : route('artikel.daftar');
    $canonical .= $halaman > 1 ? '?page='.$halaman : '';
    $deskripsi = $kategoriAktif
        ? "Kumpulan artikel {$kategoriAktif->name} Desa Adat Sangket, Sukasada, Buleleng, Bali: {$artikel->total()} tulisan terbaru dari prajuru dan krama."
        : 'Berita, pengumuman, perarem, dan profil Desa Adat Sangket, Sukasada, Buleleng, Bali. Ikuti kabar kegiatan adat dan program Kasukretan Krama.';
    $remah = [['Beranda', url('/')], ['Berita', route('artikel.daftar')]];
    if ($kategoriAktif) {
        $remah[] = [$kategoriAktif->name, $kategoriAktif->url()];
    }
    $seo = [
        'judul' => ($mencari ? "Pencarian “{$kataKunci}” – " : '').$nama.($halaman > 1 ? " – Halaman {$halaman}" : '').' | Desa Adat Sangket',
        'noindex' => $mencari,
        'deskripsi' => $deskripsi,
        'canonical' => $canonical,
        'sebelumnya' => $artikel->previousPageUrl(),
        'selanjutnya' => $artikel->nextPageUrl(),
        'jsonld' => [[
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($remah)->values()->map(fn ($item, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $item[0], 'item' => $item[1]])->all(),
        ]],
    ];
@endphp
<x-layout-publik :seo="$seo">
    <section aria-labelledby="judul-halaman" class="relative -mt-20 overflow-hidden bg-gradient-to-b from-langit-sedang via-langit-muda to-latar pt-24">
        <div aria-hidden="true" class="pointer-events-none absolute -top-40 -right-32 size-[30rem] rounded-full bg-langit/30 blur-3xl"></div>
        <div class="relative mx-auto grid max-w-6xl gap-6 px-4 pt-8 pb-10 lg:px-6">
            @include('artikel.remah', ['remah' => $remah])
            <div class="grid gap-3">
                <h1 id="judul-halaman" class="font-display text-4xl font-extrabold tracking-tight text-balance sm:text-5xl">{{ $nama }}</h1>
                <p class="max-w-2xl text-lg text-redup">{{ $deskripsi }}</p>
            </div>

            <form role="search" method="get" action="{{ $kategoriAktif ? $kategoriAktif->url() : route('artikel.daftar') }}" class="flex max-w-2xl items-center gap-2 rounded-full bg-panel p-1.5 pl-5 shadow-lg shadow-langit/10 ring-1 ring-langit-sedang focus-within:ring-2 focus-within:ring-langit">
                <svg class="size-5 shrink-0 text-redup" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                <label for="cari-berita" class="sr-only">Cari berita{{ $kategoriAktif ? ' di '.$kategoriAktif->name : '' }}</label>
                <input id="cari-berita" type="search" name="q" value="{{ $kataKunci }}" maxlength="100" placeholder="Cari berita{{ $kategoriAktif ? ' di '.$kategoriAktif->name : '' }}, misalnya piodalan" class="min-h-11 min-w-0 flex-1 bg-transparent text-base outline-none placeholder:text-redup">
                <button type="submit" class="inline-flex min-h-11 shrink-0 items-center rounded-full bg-langit px-5 font-semibold text-di-langit hover:brightness-95">Cari</button>
            </form>

            <nav aria-label="Kategori artikel" class="-mx-4 overflow-x-auto px-4 pb-1">
                <ul class="flex w-max gap-2">
                    <li>
                        <a href="{{ route('artikel.daftar', $mencari ? ['q' => $kataKunci] : []) }}" @if (! $kategoriAktif) aria-current="page" @endif
                            class="inline-flex min-h-10 items-center gap-2 rounded-full px-4 text-sm font-semibold ring-1 transition-colors {{ $kategoriAktif ? 'bg-panel ring-langit-sedang hover:bg-langit-muda' : 'bg-langit text-di-langit ring-langit' }}">
                            Semua <span class="tabular-nums opacity-70">{{ $semuaKategori->sum('articles_count') }}</span>
                        </a>
                    </li>
                    @foreach ($semuaKategori as $kategori)
                        @php($aktif = $kategoriAktif?->is($kategori))
                        <li>
                            <a href="{{ route('artikel.daftar', ['kategori' => $kategori->slug] + ($mencari ? ['q' => $kataKunci] : [])) }}" @if ($aktif) aria-current="page" @endif
                                class="inline-flex min-h-10 items-center gap-2 rounded-full px-4 text-sm font-semibold ring-1 transition-colors {{ $aktif ? 'bg-langit text-di-langit ring-langit' : 'bg-panel ring-langit-sedang hover:bg-langit-muda' }}">
                                {{ $kategori->name }} <span class="tabular-nums opacity-70">{{ $kategori->articles_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </section>

    <section aria-label="Daftar artikel" class="mx-auto grid max-w-6xl gap-8 px-4 py-6 lg:px-6">
        @if ($mencari)
            <div class="flex flex-wrap items-center justify-between gap-3">
                <p role="status" class="text-redup"><span class="font-semibold text-teks">{{ $artikel->total() }} berita</span> cocok dengan “{{ $kataKunci }}”{{ $kategoriAktif ? ' di '.$kategoriAktif->name : '' }}</p>
                <a href="{{ $kategoriAktif ? $kategoriAktif->url() : route('artikel.daftar') }}" class="text-sm font-semibold text-langit-tua underline underline-offset-4">Hapus pencarian</a>
            </div>
        @endif

        @if ($artikel->isEmpty())
            <div class="grid justify-items-center gap-3 rounded-[2rem] bg-panel p-10 text-center ring-1 ring-garis">
                <img src="/images/logo-desa-adat-sangket.png" alt="" class="size-16 object-contain opacity-70">
                @if ($mencari)
                    <h2 class="font-display text-xl font-bold">Berita tidak ditemukan</h2>
                    <p class="text-redup">Coba kata kunci lain yang lebih umum, atau cari di semua kategori.</p>
                    @if ($kategoriAktif)
                        <a href="{{ route('artikel.daftar', ['q' => $kataKunci]) }}" class="mt-2 inline-flex min-h-11 items-center rounded-full bg-langit px-5 font-semibold text-di-langit">Cari di semua berita</a>
                    @endif
                @else
                    <h2 class="font-display text-xl font-bold">Belum ada berita di kategori ini</h2>
                    <p class="text-redup">Berita terbaru dari kategori lain tetap bisa dibaca.</p>
                    <a href="{{ route('artikel.daftar') }}" class="mt-2 inline-flex min-h-11 items-center rounded-full bg-langit px-5 font-semibold text-di-langit">Lihat semua berita</a>
                @endif
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($artikel as $item)
                    @include('artikel.kartu', ['artikel' => $item, 'unggulan' => $loop->first && $halaman === 1 && ! $mencari && $artikel->count() > 2])
                @endforeach
            </div>

            @if ($artikel->hasPages())
                <nav aria-label="Halaman artikel" class="flex flex-wrap items-center justify-center gap-2">
                    @if ($artikel->onFirstPage())
                        <span class="inline-flex min-h-11 items-center rounded-full px-4 text-sm text-redup ring-1 ring-garis opacity-60">Sebelumnya</span>
                    @else
                        <a href="{{ $artikel->previousPageUrl() }}" rel="prev" class="inline-flex min-h-11 items-center rounded-full bg-panel px-4 text-sm font-semibold ring-1 ring-garis hover:bg-langit-muda">Sebelumnya</a>
                    @endif
                    @foreach ($artikel->getUrlRange(1, $artikel->lastPage()) as $nomor => $tautan)
                        <a href="{{ $tautan }}" @if ($nomor === $halaman) aria-current="page" @endif
                            class="grid size-11 place-items-center rounded-full text-sm font-semibold ring-1 {{ $nomor === $halaman ? 'bg-langit text-di-langit ring-langit' : 'bg-panel ring-garis hover:bg-langit-muda' }}">{{ $nomor }}</a>
                    @endforeach
                    @if ($artikel->hasMorePages())
                        <a href="{{ $artikel->nextPageUrl() }}" rel="next" class="inline-flex min-h-11 items-center rounded-full bg-panel px-4 text-sm font-semibold ring-1 ring-garis hover:bg-langit-muda">Selanjutnya</a>
                    @else
                        <span class="inline-flex min-h-11 items-center rounded-full px-4 text-sm text-redup ring-1 ring-garis opacity-60">Selanjutnya</span>
                    @endif
                </nav>
            @endif
        @endif
    </section>
</x-layout-publik>
