@php
    $seo = ['judul' => 'Halaman tidak ditemukan | Desa Adat Sangket', 'deskripsi' => 'Halaman yang Anda cari tidak tersedia di situs Desa Adat Sangket.', 'noindex' => true];
@endphp
<x-layout-publik :seo="$seo">
    <section aria-labelledby="judul-404" class="relative -mt-20 bg-gradient-to-b from-langit-sedang via-langit-muda to-latar pt-24">
        <div class="mx-auto grid max-w-2xl justify-items-center gap-4 px-4 py-20 text-center">
            <p class="font-display text-7xl font-extrabold text-langit-tua">404</p>
            <h1 id="judul-404" class="font-display text-3xl font-bold text-balance">Halaman tidak ditemukan</h1>
            <p class="text-lg text-redup">Artikel atau kategori ini mungkin sudah dipindahkan. Silakan lihat berita terbaru.</p>
            <div class="mt-2 flex flex-wrap justify-center gap-3">
                <a href="{{ route('artikel.daftar') }}" class="inline-flex min-h-12 items-center rounded-full bg-langit px-6 font-semibold text-di-langit">Berita</a>
                <a href="{{ url('/') }}" class="inline-flex min-h-12 items-center rounded-full bg-panel px-6 font-semibold ring-1 ring-garis">Beranda</a>
            </div>
        </div>
    </section>
</x-layout-publik>
