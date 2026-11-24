{{-- resources/views/components/chip.blade.php label kecil dengan tone semantik dan ikon --}}
@props(['tone' => 'neutral'])
@php
    $classes = match ($tone) {
        'green' => 'bg-green/14 text-green',
        'orange' => 'bg-orange/14 text-orange',
        'pink' => 'bg-pink/14 text-pink',
        'blue' => 'bg-blue/14 text-blue',
        'white' => 'bg-white text-black',
        'gray' => 'bg-gray text-black',
        'solid-green' => 'bg-green text-black',
        'solid-orange' => 'bg-orange text-black',
        'solid-pink' => 'bg-pink text-black',
        'solid-blue' => 'bg-blue text-black',
        'solid-gray' => 'bg-gray text-black',
        default => 'bg-surface-3 text-white',
    };
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-micro font-semibold $classes"]) }}>
    @isset($icon)
        {{ $icon }}
    @endisset
    {{ $slot }}
</span>
