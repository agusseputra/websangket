{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('profil') }}</loc>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('lpd') }}</loc>
        <changefreq>monthly</changefreq>
    </url>
    <url>
        <loc>{{ route('artikel.daftar') }}</loc>
        @if ($artikel->first()?->created_at)
            <lastmod>{{ $artikel->first()->created_at->toAtomString() }}</lastmod>
        @endif
        <changefreq>weekly</changefreq>
    </url>
    @foreach ($semuaKategori as $kategori)
        <url>
            <loc>{{ $kategori->url() }}</loc>
            <changefreq>weekly</changefreq>
        </url>
    @endforeach
    @foreach ($artikel as $item)
        <url>
            <loc>{{ $item->url() }}</loc>
            @if ($item->updated_at ?? $item->created_at)
                <lastmod>{{ ($item->updated_at ?? $item->created_at)->toAtomString() }}</lastmod>
            @endif
        </url>
    @endforeach
</urlset>
