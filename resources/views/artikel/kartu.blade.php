@php
    $judulTag ??= 'h2';
    $unggulan ??= false;
    $gambar = $artikel->gambarUrl();
@endphp
<article class="group relative flex h-full flex-col overflow-hidden rounded-[2rem] bg-panel ring-1 ring-garis transition hover:-translate-y-1 hover:shadow-xl hover:shadow-langit/10 motion-reduce:transition-none {{ $unggulan ? 'md:col-span-2 md:flex-row' : '' }}">
    <div class="relative shrink-0 overflow-hidden {{ $unggulan ? 'aspect-[16/10] md:aspect-auto md:w-1/2' : 'aspect-[16/10]' }}">
        @if ($gambar)
            <img src="{{ $gambar }}" alt="" loading="lazy" decoding="async" class="size-full object-cover transition duration-500 group-hover:scale-105 motion-reduce:transition-none">
        @else
            <div class="grid size-full place-items-center bg-gradient-to-br from-langit-sedang via-langit-muda to-panel">
                <img src="/images/logo-desa-adat-sangket.png" alt="" class="size-16 object-contain opacity-80">
            </div>
        @endif
        <span class="absolute top-4 left-4 rounded-full bg-panel/90 px-3 py-1 text-xs font-semibold text-langit-tua shadow-sm backdrop-blur">{{ $artikel->category->name }}</span>
    </div>
    <div class="flex flex-1 flex-col gap-3 p-6 {{ $unggulan ? 'md:justify-center md:p-10' : '' }}">
        <p class="text-sm text-redup"><time datetime="{{ $artikel->created_at?->toIso8601String() }}">{{ $artikel->tanggalTampil() }}</time> · {{ $artikel->menitBaca() }} menit baca</p>
        <{{ $judulTag }} class="font-display font-bold text-balance {{ $unggulan ? 'text-2xl sm:text-3xl' : 'text-lg' }}">
            <a href="{{ $artikel->url() }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline">{{ $artikel->title }}</a>
        </{{ $judulTag }}>
        <p class="text-sm text-redup {{ $unggulan ? 'line-clamp-4' : 'line-clamp-3' }}">{{ $artikel->ringkasan(180) }}</p>
    </div>
</article>
