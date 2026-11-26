{{-- resources/views/components/bento/icon-badge.blade.php lingkaran ikon untuk status dan hasil --}}
@props(['icon', 'size' => 'md', 'tone' => 'glass'])

@php
    $sizes = ['sm' => ['h-10 w-10', 20], 'md' => ['h-11 w-11', 22], 'lg' => ['h-12 w-12', 24]];
    [$sizeClasses, $iconSize] = $sizes[$size] ?? $sizes['md'];
    $toneClasses = [
        'glass' => 'bg-white/70 text-black',
        'green' => 'bg-green text-black',
        'pink' => 'bg-pink text-black',
        'surface' => 'bg-surface-3 text-white',
    ][$tone] ?? 'bg-white/70 text-black';
@endphp

<span {{ $attributes->class(['grid shrink-0 place-items-center rounded-full', $sizeClasses, $toneClasses]) }}>
    <x-icon :name="$icon" :size="$iconSize" />
</span>