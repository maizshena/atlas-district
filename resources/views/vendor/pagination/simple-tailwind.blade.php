{{-- resources/views/vendor/pagination/simple-tailwind.blade.php navigasi halaman sederhana --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex items-center gap-2">
        @if ($paginator->onFirstPage())
            <span class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-gray/60">Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-white transition bento-hover:border-border-strong">Sebelumnya</a>
        @endif
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-white transition bento-hover:border-border-strong">Berikutnya</a>
        @else
            <span class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-gray/60">Berikutnya</span>
        @endif
    </nav>
@endif
