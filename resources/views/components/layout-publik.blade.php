@props(['seo'])
@php
    $judul = $seo['judul'];
    $gambar = $seo['gambar'] ?? url('/images/og-dudukan-sangket.jpg');
    $logo = url('/images/logo-desa-adat-sangket.png');
@endphp
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#0369a1">
        <title>{{ $judul }}</title>
        <script>
            try {
                const tema = localStorage.getItem('dudukan-tema');
                if (tema === 'light' || tema === 'dark') document.documentElement.dataset.theme = tema;
            } catch {}
        </script>
        <meta name="description" content="{{ $seo['deskripsi'] }}">
        @isset($seo['canonical'])
            <link rel="canonical" href="{{ $seo['canonical'] }}">
        @endisset
        @if ($seo['noindex'] ?? false)
            <meta name="robots" content="noindex, follow">
        @endif
        @isset($seo['sebelumnya'])
            <link rel="prev" href="{{ $seo['sebelumnya'] }}">
        @endisset
        @isset($seo['selanjutnya'])
            <link rel="next" href="{{ $seo['selanjutnya'] }}">
        @endisset
        <meta property="og:type" content="{{ $seo['tipe'] ?? 'website' }}">
        <meta property="og:site_name" content="Desa Adat Sangket">
        <meta property="og:locale" content="id_ID">
        <meta property="og:url" content="{{ $seo['canonical'] ?? url()->current() }}">
        <meta property="og:title" content="{{ $seo['og_judul'] ?? $judul }}">
        <meta property="og:description" content="{{ $seo['deskripsi'] }}">
        <meta property="og:image" content="{{ $gambar }}">
        <meta property="og:image:alt" content="{{ $seo['gambar_alt'] ?? 'Logo dan suasana Desa Adat Sangket' }}">
        @unless (isset($seo['gambar']))
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
        @endunless
        @foreach ($seo['og_artikel'] ?? [] as $properti => $nilai)
            <meta property="article:{{ $properti }}" content="{{ $nilai }}">
        @endforeach
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seo['og_judul'] ?? $judul }}">
        <meta name="twitter:description" content="{{ $seo['deskripsi'] }}">
        <meta name="twitter:image" content="{{ $gambar }}">
        @foreach ($seo['jsonld'] ?? [] as $data)
            <script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
        @endforeach
        <link rel="icon" href="/favicon.ico" sizes="48x48">
        <link rel="icon" type="image/png" href="/images/logo-desa-adat-sangket.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
        <link rel="manifest" href="/manifest.webmanifest">
        <link rel="alternate" type="application/xml" title="Peta situs" href="{{ route('sitemap') }}">
        @fonts
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-dvh bg-latar antialiased">
        <a href="#konten" class="sr-only z-50 rounded-full bg-langit px-4 py-2 font-semibold text-di-langit focus:not-sr-only focus:fixed focus:top-3 focus:left-3">Lewati ke konten</a>
        @include('publik.navbar')
        <main id="konten">
            {{ $slot }}
        </main>
        @include('publik.footer')
    </body>
</html>
