@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-400">Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ $paginator->total() }} data</p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-600">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-lg border border-white/15 px-3 py-2 text-sm text-slate-200 hover:bg-white/10">Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 py-2 text-sm text-slate-500">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="rounded-lg bg-indigo-500 px-3 py-2 text-sm font-semibold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="rounded-lg border border-white/15 px-3 py-2 text-sm text-slate-200 hover:bg-white/10">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-lg border border-white/15 px-3 py-2 text-sm text-slate-200 hover:bg-white/10">Berikutnya</a>
            @else
                <span class="rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-600">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
