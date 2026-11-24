{{-- resources/views/components/admin/data-tile.blade.php tile pembungkus tabel admin yang dapat digulir --}}
@props(['title' => null])
<x-bento.tile span="6x2" class="!p-0">
    @if ($title)
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-border-token px-4 py-3.5 lg:px-6">
            <h2 class="text-heading font-semibold text-white">{{ $title }}</h2>
            @isset($actions)
                <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </div>
    @endif
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>
</x-bento.tile>
