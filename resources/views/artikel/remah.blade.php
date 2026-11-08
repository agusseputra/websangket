<nav aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-redup">
        @foreach ($remah as [$label, $tautan])
            <li class="flex min-w-0 items-center gap-2">
                @if ($loop->last)
                    <span aria-current="page" class="line-clamp-1 font-medium text-teks">{{ $label }}</span>
                @else
                    <a href="{{ $tautan }}" class="hover:text-langit-tua hover:underline">{{ $label }}</a>
                    <svg class="size-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m9 6 6 6-6 6"/></svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
