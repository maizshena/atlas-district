{{-- resources/views/vendor/pagination/tailwind.blade.php navigasi halaman dengan token tema --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-caption text-gray">Menampilkan <span class="num text-white">{{ $paginator->firstItem() }}</span> sampai <span class="num text-white">{{ $paginator->lastItem() }}</span> dari <span class="num text-white">{{ $paginator->total() }}</span> data</p>
        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-gray/60">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-white transition bento-hover:border-border-strong">Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-caption text-gray">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="flex min-h-11 min-w-11 items-center justify-center rounded-btn bg-white px-3 text-caption font-bold text-black">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="flex min-h-11 min-w-11 items-center justify-center rounded-btn border border-border-token px-3 text-caption text-white transition bento-hover:border-border-strong">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-white transition bento-hover:border-border-strong">Berikutnya</a>
            @else
                <span class="flex min-h-11 items-center rounded-btn border border-border-token px-3 text-caption text-gray/60">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
