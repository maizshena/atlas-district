{{-- resources/views/components/bento/stat.blade.php isi tile angka besar dengan label --}}
@props(['value', 'label', 'hint' => null, 'delta' => null, 'icon' => null, 'flash' => null, 'count' => null])
<div class="flex h-full flex-col">
    <div class="flex items-start justify-between gap-2">
        <p class="text-micro font-semibold uppercase tracking-wider opacity-70">{{ $label }}</p>
        @if ($icon)
            <span class="opacity-80"><x-icon :name="$icon" :size="18" /></span>
        @endif
    </div>
    @if ($count !== null)
        <p class="num mt-2 break-words text-title" x-data="countUp(@js((int) $count))" data-count><span x-text="shown">{{ $value }}</span></p>
    @else
        <p @class(['mt-2 text-title break-words num', 'text-display' => $flash === 'display'])>{{ $value }}</p>
    @endif
    @if ($delta)
        <p class="mt-1 text-micro font-semibold">{{ $delta }}</p>
    @endif
    @if ($hint)
        <p class="mt-auto pt-3 text-micro sub">{{ $hint }}</p>
    @endif
    {{ $slot }}
</div>
