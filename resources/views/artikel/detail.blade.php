@php
    $kategori = $artikel->category;
    $gambar = $artikel->gambarUrl();
    $deskripsi = $artikel->ringkasan();
    $terbit = $artikel->created_at?->toIso8601String();
    $diubah = ($artikel->updated_at ?? $artikel->created_at)?->toIso8601String();
    $remah = [['Beranda', url('/')], ['Berita', route('artikel.daftar')], [$kategori->name, $kategori->url()], [$artikel->title, $artikel->url()]];
    $penerbit = ['@type' => 'Organization', 'name' => 'Desa Adat Sangket', 'url' => url('/'), 'logo' => ['@type' => 'ImageObject', 'url' => url('/images/logo-desa-adat-sangket.png')]];
    $seo = [
        'judul' => $artikel->title.' | Desa Adat Sangket',
        'og_judul' => $artikel->title,
        'deskripsi' => $deskripsi,
        'canonical' => $artikel->url(),
        'tipe' => 'article',
        'gambar' => $gambar,
        'gambar_alt' => $artikel->title,
        'og_artikel' => array_filter(['published_time' => $terbit, 'modified_time' => $diubah, 'section' => $kategori->name]),
        'jsonld' => [
            array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'headline' => $artikel->title,
                'description' => $deskripsi,
                'image' => [$gambar ?? url('/images/og-dudukan-sangket.jpg')],
                'datePublished' => $terbit,
                'dateModified' => $diubah,
                'articleSection' => $kategori->name,
                'inLanguage' => 'id-ID',
                'mainEntityOfPage' => $artikel->url(),
                'author' => $penerbit,
                'publisher' => $penerbit,
            ]),
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($remah)->values()->map(fn ($item, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $item[0], 'item' => $item[1]])->all(),
            ],
        ],
    ];
    $teksBagikan = rawurlencode($artikel->title.' '.$artikel->url());
@endphp
<x-layout-publik :seo="$seo">
    <article aria-labelledby="judul-artikel">
        <header class="relative -mt-20 overflow-hidden bg-gradient-to-b from-langit-sedang via-langit-muda to-latar pt-24">
            <div aria-hidden="true" class="pointer-events-none absolute -top-40 -left-32 size-[34rem] rounded-full bg-langit/30 blur-3xl"></div>
            <div class="relative mx-auto grid max-w-6xl gap-6 px-4 pt-8 pb-10 lg:px-6">
                @include('artikel.remah', ['remah' => $remah])
                <div class="grid max-w-4xl gap-5">
                    <a href="{{ $kategori->url() }}" class="justify-self-start rounded-full bg-panel px-3 py-1 text-sm font-semibold text-langit-tua ring-1 ring-langit-sedang hover:bg-langit-muda">{{ $kategori->name }}</a>
                    <h1 id="judul-artikel" class="font-display text-3xl leading-tight font-extrabold tracking-tight text-balance sm:text-5xl">{{ $artikel->title }}</h1>
                    <dl class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-redup">
                        <div class="flex items-center gap-2">
                            <dt class="sr-only">Terbit</dt>
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                            <dd><time datetime="{{ $terbit }}">{{ $artikel->tanggalTampil() }}</time></dd>
                        </div>
                        <div class="flex items-center gap-2">
                            <dt class="sr-only">Lama baca</dt>
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                            <dd>{{ $artikel->menitBaca() }} menit baca</dd>
                        </div>
                        <div class="flex items-center gap-2">
                            <dt class="sr-only">Penulis</dt>
                            <img src="/images/logo-desa-adat-sangket.png" alt="" class="size-5 object-contain">
                            <dd>Desa Adat Sangket</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </header>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 lg:grid-cols-[minmax(0,1fr)_20rem] lg:px-6">
            <div class="grid min-w-0 content-start gap-8">
                @if ($gambar)
                    <figure class="grid gap-3">
                        <img src="{{ $gambar }}" alt="{{ $artikel->title }}" fetchpriority="high" class="max-h-[34rem] w-full rounded-[2rem] object-cover shadow-xl shadow-langit/10">
                        <figcaption class="text-sm text-redup">Dokumentasi Desa Adat Sangket · {{ $kategori->name }}</figcaption>
                    </figure>
                @endif

                <section aria-label="Isi artikel" class="isi-artikel rounded-[2rem] bg-panel p-6 ring-1 ring-garis sm:p-10">
                    {!! $isi !!}
                </section>

                <footer class="grid gap-6">
                    <div class="flex flex-wrap items-center gap-3 rounded-[1.5rem] bg-panel p-4 ring-1 ring-garis">
                        <p class="mr-auto font-semibold">Bagikan artikel ini</p>
                        <a href="https://wa.me/?text={{ $teksBagikan }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-[#25d366] px-4 text-sm font-semibold text-[#052e16] hover:brightness-95">WhatsApp</a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($artikel->url()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-[#1877f2] px-4 text-sm font-semibold text-white hover:brightness-95">Facebook</a>
                        <button type="button" data-salin="{{ $artikel->url() }}" class="inline-flex min-h-11 items-center gap-2 rounded-full px-4 text-sm font-semibold ring-1 ring-garis hover:bg-langit-muda">
                            <span data-label>Salin tautan</span>
                        </button>
                    </div>

                    @if ($sebelumnya || $selanjutnya)
                        <nav aria-label="Artikel lain" class="grid gap-3 sm:grid-cols-2">
                            @if ($sebelumnya)
                                <a href="{{ $sebelumnya->url() }}" rel="prev" class="group grid gap-1 rounded-[1.5rem] bg-panel p-5 ring-1 ring-garis hover:ring-langit">
                                    <span class="text-sm text-redup">← Artikel sebelumnya</span>
                                    <span class="line-clamp-2 font-semibold group-hover:text-langit-tua">{{ $sebelumnya->title }}</span>
                                </a>
                            @endif
                            @if ($selanjutnya)
                                <a href="{{ $selanjutnya->url() }}" rel="next" class="group grid gap-1 rounded-[1.5rem] bg-panel p-5 text-right ring-1 ring-garis hover:ring-langit sm:col-start-2">
                                    <span class="text-sm text-redup">Artikel selanjutnya →</span>
                                    <span class="line-clamp-2 font-semibold group-hover:text-langit-tua">{{ $selanjutnya->title }}</span>
                                </a>
                            @endif
                        </nav>
                    @endif
                </footer>
            </div>

            <x-sidebar-publik label="Navigasi artikel" :kategori-aktif="$kategori" :kecuali-artikel="$artikel->id" />
        </div>
    </article>

    @if ($terkait->isNotEmpty())
        <section aria-labelledby="judul-terkait" class="mx-auto mt-16 grid max-w-6xl gap-6 px-4 lg:px-6">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 id="judul-terkait" class="font-display text-2xl font-bold sm:text-3xl">Artikel terkait</h2>
                <a href="{{ $kategori->url() }}" class="text-sm font-semibold text-langit-tua underline underline-offset-4">Lihat semua {{ $kategori->name }}</a>
            </div>
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($terkait as $item)
                    @include('artikel.kartu', ['artikel' => $item, 'judulTag' => 'h3'])
                @endforeach
            </div>
        </section>
    @endif

    <script>
        document.querySelectorAll('[data-salin]').forEach((tombol) => {
            tombol.addEventListener('click', async () => {
                const label = tombol.querySelector('[data-label]');
                try {
                    await navigator.clipboard.writeText(tombol.dataset.salin);
                    label.textContent = 'Tautan disalin';
                } catch {
                    label.textContent = 'Gagal menyalin';
                }
                setTimeout(() => { label.textContent = 'Salin tautan'; }, 2000);
            });
        });
    </script>
</x-layout-publik>
