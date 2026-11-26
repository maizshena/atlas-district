{{-- resources/views/components/event/fact-tile.blade.php tile fakta singkat event dengan label ikon dan keterangan --}}
@props(['label', 'icon', 'value'])

<x-bento.tile span="2x1" {{ $attributes }}>
    <x-bento.eyebrow>{{ $label }}</x-bento.eyebrow>
    <p class="mt-2 flex items-center gap-2 text-heading font-semibold text-white">
        <x-icon :name="$icon" :size="18" /> {{ $value }}
    </p>
    <p class="mt-1 text-caption text-gray">{{ $slot }}</p>
</x-bento.tile>