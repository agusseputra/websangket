{{-- Satu navbar untuk landing page (React) dan halaman berita (Blade). --}}
@php
    $menu = [
        ['href' => route('profil'), 'label' => 'Profil', 'aktif' => request()->routeIs('profil')],
        ['href' => url('/#kasukretan-krama'), 'label' => 'Kasukretan Krama', 'aktif' => false],
        ['href' => route('artikel.daftar'), 'label' => 'Berita', 'aktif' => request()->routeIs('artikel.*')],
        ['href' => route('lpd'), 'label' => 'LPD', 'aktif' => request()->routeIs('lpd')],
        ['href' => url('/#cek-pembayaran'), 'label' => 'Cek pembayaran', 'aktif' => false],
    ];
    $sorot = ['href' => url('/#pelaporan-tinggal'), 'label' => 'Pelaporan', 'aktif' => false];
@endphp
<header id="navbar-publik" class="sticky top-0 z-30 px-3 pt-[max(0.75rem,env(safe-area-inset-top))]">
    <div class="mx-auto max-w-6xl rounded-[1.75rem] border border-langit-sedang/70 bg-panel/85 shadow-lg shadow-langit/10 backdrop-blur-md">
        <div class="flex items-center justify-between gap-4 py-1.5 pr-1.5 pl-3">
            <a href="{{ url('/') }}" class="flex min-w-0 items-center gap-2.5">
                <img src="/images/logo-desa-adat-sangket.png" alt="Logo Desa Adat Sangket" width="40" height="39" class="size-10 shrink-0 object-contain">
                <span class="truncate font-display font-bold">Desa Adat Sangket</span>
            </a>

            <nav aria-label="Menu utama" class="hidden items-center gap-1 lg:flex">
                @foreach ($menu as $item)
                    <a href="{{ $item['href'] }}" @if ($item['aktif']) aria-current="page" @endif
                        class="rounded-full px-4 py-2 text-sm font-semibold transition-colors {{ $item['aktif'] ? 'bg-langit-muda text-langit-tua' : 'hover:bg-langit-muda' }}">{{ $item['label'] }}</a>
                @endforeach
                <a href="{{ $sorot['href'] }}" class="rounded-full bg-langit px-4 py-2 text-sm font-semibold text-di-langit hover:brightness-95">{{ $sorot['label'] }}</a>
            </nav>

            <details class="group relative lg:hidden">
                <summary class="grid size-11 cursor-pointer list-none place-items-center rounded-full hover:bg-langit-muda [&::-webkit-details-marker]:hidden" aria-label="Menu">
                    <svg class="size-5 group-open:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="hidden size-5 group-open:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </summary>
                <nav aria-label="Menu utama" class="absolute top-14 right-0 grid w-64 gap-1 rounded-3xl border border-langit-sedang/70 bg-panel p-2 shadow-xl">
                    @foreach ([...$menu, $sorot] as $item)
                        <a href="{{ $item['href'] }}" @if ($item['aktif']) aria-current="page" @endif
                            class="flex min-h-11 items-center rounded-2xl px-3 font-semibold {{ $loop->last ? 'mt-1 bg-langit text-di-langit' : ($item['aktif'] ? 'bg-langit-muda text-langit-tua' : 'hover:bg-langit-muda') }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </details>
        </div>
    </div>
    <script>
        // Menu ponsel ditutup setelah tautan dipilih, termasuk tautan ke bagian di halaman yang sama.
        document.querySelectorAll('#navbar-publik details a').forEach((tautan) => {
            tautan.addEventListener('click', () => tautan.closest('details').removeAttribute('open'));
        });
    </script>
</header>
