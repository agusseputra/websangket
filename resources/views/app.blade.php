@php
    $beranda = request()->is('/');
    $judul = $beranda ? 'Desa Adat Sangket' : config('app.name');
    $deskripsi = 'Desa Adat Sangket, Sukasada, Buleleng, Bali. Melalui program prioritas Kasukretan Krama, cek tunggakan dudukan, kenali jenis krama, dan lapor diri 1×24 jam.';
    $gambar = url('/images/og-dudukan-sangket.jpg');
@endphp
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#0369a1">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <title>{{ $judul }}</title>
        <script>
            // Diterapkan sebelum halaman digambar agar tema tidak berkedip saat dimuat.
            try {
                const tema = localStorage.getItem('dudukan-tema');
                if (tema === 'light' || tema === 'dark') document.documentElement.dataset.theme = tema;
            } catch {}
        </script>
        <meta name="description" content="{{ $deskripsi }}">
        @if ($beranda)
            <link rel="canonical" href="{{ url('/') }}">
            <meta property="og:type" content="website">
            <meta property="og:site_name" content="Desa Adat Sangket">
            <meta property="og:locale" content="id_ID">
            <meta property="og:url" content="{{ url('/') }}">
            <meta property="og:title" content="{{ $judul }}">
            <meta property="og:description" content="{{ $deskripsi }}">
            <meta property="og:image" content="{{ $gambar }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
            <meta property="og:image:alt" content="Candi bentar Bali dengan logo dan tulisan Desa Adat Sangket">
            <meta name="twitter:card" content="summary_large_image">
            <meta name="twitter:title" content="{{ $judul }}">
            <meta name="twitter:description" content="{{ $deskripsi }}">
            <meta name="twitter:image" content="{{ $gambar }}">
            <link rel="preload" as="image" href="/images/landing/bali/mapeed.jpg" fetchpriority="high">
        @else
            <meta name="robots" content="noindex, nofollow">
        @endif
        <meta name="recaptcha-site-key" content="{{ config('services.recaptcha.site_key') }}">
        <link rel="icon" href="/favicon.ico" sizes="48x48">
        <link rel="icon" type="image/png" href="/images/logo-desa-adat-sangket.png">
        <link rel="manifest" href="/manifest.webmanifest">
        <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
        @fonts
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/main.jsx'])
    </head>
    <body>
        @if ($beranda)
            @include('publik.navbar')
        @endif
        <div id="app"></div>
    </body>
</html>
