@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex items-center gap-2">
        @if ($paginator->onFirstPage())
            <span class="rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-600">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-lg border border-white/15 px-3 py-2 text-sm text-slate-200 hover:bg-white/10">Sebelumnya</a>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-lg border border-white/15 px-3 py-2 text-sm text-slate-200 hover:bg-white/10">Berikutnya</a>
        @else
            <span class="rounded-lg border border-white/10 px-3 py-2 text-sm text-slate-600">Berikutnya</span>
        @endif
    </nav>
@endif
