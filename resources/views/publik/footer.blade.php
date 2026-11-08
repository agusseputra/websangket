@php
    $kategoriFooter = \App\Models\Category::query()->has('articles')->orderBy('id')->get();
    $fotografer = [
        ['Ruben Hutabarat', 'https://unsplash.com/photos/VvJ0DL_PLR8'],
        ['Didi Paul', 'https://unsplash.com/photos/fWvv8L9Ann0'],
        ['Niklas Weiss', 'https://unsplash.com/photos/-2WlTWZLnRc'],
        ['Jeremy Bishop', 'https://unsplash.com/photos/QUwLZNchflk'],
        ['Sebastian Pena Lambarri', 'https://unsplash.com/photos/U_i6h9Y50wQ'],
    ];
@endphp
<footer class="mt-16 bg-[#0a0a0a] text-neutral-300 [color-scheme:dark]">
    <div aria-hidden="true" class="pita-poleng-merah h-4 w-full"></div>
    <div class="mx-auto grid max-w-6xl gap-10 px-4 pt-12 pb-[max(2.5rem,env(safe-area-inset-bottom))] md:grid-cols-[1.6fr_1fr_1fr] lg:px-6">
        <div class="grid content-start gap-4">
            <div class="flex items-center gap-3">
                <img src="/images/logo-desa-adat-sangket.png" alt="" class="size-14 object-contain">
                <div>
                    <p class="font-display text-lg font-bold text-white">Desa Adat Sangket</p>
                    <p class="text-sm text-neutral-400">Kecamatan Sukasada, Kabupaten Buleleng, Bali</p>
                </div>
            </div>
            <p class="max-w-md text-sm text-neutral-400"><span class="font-semibold text-neutral-200">Wewidangan:</span> Timur Sungai Padang Bulia, selatan Desa Lumbanan, barat Sungai Sambangan, dan utara Desa Bantang Banua.</p>
            <p class="text-sm text-neutral-400">Bendesa Adat: <span class="font-medium whitespace-nowrap text-neutral-200">+62 878-6306-1763</span></p>
        </div>

        <nav aria-label="Kategori artikel" class="grid content-start gap-2 text-sm">
            <p class="font-semibold text-white">Berita</p>
            <a href="{{ route('artikel.daftar') }}" class="text-neutral-400 hover:text-white">Semua berita</a>
            @foreach ($kategoriFooter as $kategori)
                <a href="{{ $kategori->url() }}" class="text-neutral-400 hover:text-white">{{ $kategori->name }}</a>
            @endforeach
        </nav>

        <nav aria-label="Layanan" class="grid content-start gap-2 text-sm">
            <p class="font-semibold text-white">Layanan krama</p>
            <a href="{{ route('profil') }}" class="text-neutral-400 hover:text-white">Profil desa adat</a>
            <a href="{{ route('lpd') }}" class="text-neutral-400 hover:text-white">LPD Desa Adat Sangket</a>
            <a href="{{ url('/#cek-pembayaran') }}" class="text-neutral-400 hover:text-white">Cek pembayaran dudukan</a>
            <a href="{{ url('/#pelaporan-tinggal') }}" class="text-neutral-400 hover:text-white">Lapor tinggal atau usaha</a>
            <a href="{{ url('/login') }}" class="text-neutral-400 hover:text-white">Masuk petugas</a>
        </nav>
    </div>

    <div class="border-t border-neutral-800 px-4 py-4 text-center text-xs text-neutral-500">
        <p>© {{ now()->year }} Desa Adat Sangket · Foto dokumentasi Desa Adat Sangket</p>
        <p class="mt-1">Foto suasana Bali dari Unsplash:
            @foreach ($fotografer as [$nama, $tautan])
                <a href="{{ $tautan }}" target="_blank" rel="noreferrer" class="underline underline-offset-2 hover:text-neutral-300">{{ $nama }}</a>{{ $loop->last ? '' : ',' }}
            @endforeach
        </p>
    </div>
</footer>
