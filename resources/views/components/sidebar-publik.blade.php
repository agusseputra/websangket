{{-- Sidebar bersama untuk detail berita dan halaman profil. Isi tambahan (misalnya daftar isi) masuk lewat slot. --}}
<aside aria-label="{{ $label }}" class="grid content-start gap-5 lg:sticky lg:top-24 lg:max-h-[calc(100dvh-7rem)] lg:overflow-y-auto lg:pb-4">
    {{ $slot }}

    <form role="search" method="get" action="{{ route('artikel.daftar') }}" class="flex items-center gap-2 rounded-full bg-panel p-1.5 pl-4 ring-1 ring-garis focus-within:ring-2 focus-within:ring-langit">
        <label for="cari-berita-samping" class="sr-only">Cari berita</label>
        <input id="cari-berita-samping" type="search" name="q" maxlength="100" placeholder="Cari berita" class="min-h-10 min-w-0 flex-1 bg-transparent text-sm outline-none placeholder:text-redup">
        <button type="submit" class="grid size-10 shrink-0 place-items-center rounded-full bg-langit text-di-langit hover:brightness-95" aria-label="Cari">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        </button>
    </form>

    <section aria-labelledby="judul-kategori" class="rounded-[1.75rem] bg-panel p-5 ring-1 ring-garis">
        <h2 id="judul-kategori" class="font-display text-lg font-bold">Kategori</h2>
        <ul class="mt-3 grid gap-1">
            @foreach ($semuaKategori as $satu)
                @php($aktif = $kategoriAktif?->is($satu))
                <li>
                    <a href="{{ $satu->url() }}" @if ($aktif) aria-current="true" @endif class="flex min-h-10 items-center justify-between gap-3 rounded-xl px-3 text-sm font-medium {{ $aktif ? 'bg-langit-muda text-langit-tua' : 'hover:bg-langit-muda' }}">
                        {{ $satu->name }}
                        <span class="rounded-full bg-latar px-2 py-0.5 text-xs tabular-nums text-redup">{{ $satu->articles_count }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    @if ($terbaru->isNotEmpty())
        <section aria-labelledby="judul-terbaru" class="rounded-[1.75rem] bg-panel p-5 ring-1 ring-garis">
            <h2 id="judul-terbaru" class="font-display text-lg font-bold">Artikel terbaru</h2>
            <ol class="mt-3 grid gap-3">
                @foreach ($terbaru as $satu)
                    @php($gambarKecil = $satu->gambarUrl())
                    <li>
                        <a href="{{ $satu->url() }}" class="group flex gap-3">
                            @if ($gambarKecil)
                                <img src="{{ $gambarKecil }}" alt="" loading="lazy" class="size-16 shrink-0 rounded-2xl object-cover">
                            @else
                                <span class="grid size-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-langit-sedang to-langit-muda font-display text-lg font-extrabold text-langit-tua" aria-hidden="true">{{ $loop->iteration }}</span>
                            @endif
                            <span class="grid content-center gap-1">
                                <span class="line-clamp-2 text-sm font-semibold group-hover:text-langit-tua group-hover:underline">{{ $satu->title }}</span>
                                <time datetime="{{ $satu->created_at?->toIso8601String() }}" class="text-xs text-redup">{{ $satu->tanggalTampil() }}</time>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ol>
            <a href="{{ route('artikel.daftar') }}" class="mt-4 inline-flex text-sm font-semibold text-langit-tua underline underline-offset-4">Semua artikel</a>
        </section>
    @endif

    <section aria-labelledby="judul-layanan" class="rounded-[1.75rem] bg-gradient-to-br from-langit to-langit-sedang p-5 text-di-langit">
        <h2 id="judul-layanan" class="font-display text-lg font-bold">Layanan krama</h2>
        <p class="mt-1 text-sm">Cek tunggakan dudukan atau lapor tinggal tanpa perlu membuat akun.</p>
        <div class="mt-4 grid gap-2">
            <a href="{{ url('/#cek-pembayaran') }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#082f49] px-4 text-sm font-semibold text-white hover:brightness-110">Cek pembayaran</a>
            <a href="{{ url('/#pelaporan-tinggal') }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-white/70 px-4 text-sm font-semibold text-[#082f49] hover:bg-white">Lapor tinggal</a>
        </div>
    </section>

    <a href="{{ route('profil') }}" @if (request()->routeIs('profil')) aria-current="page" @endif class="group grid gap-1 rounded-[1.75rem] bg-panel p-5 ring-1 ring-garis hover:ring-langit">
        <span class="flex items-center gap-3">
            <img src="/images/logo-desa-adat-sangket.png" alt="" class="size-10 object-contain">
            <span class="font-display text-lg font-bold group-hover:text-langit-tua">Profil Desa Adat</span>
        </span>
        <span class="text-sm text-redup">Sejarah, prajuru, lembaga adat, dan kegiatan rutin Desa Adat Sangket.</span>
    </a>
</aside>
