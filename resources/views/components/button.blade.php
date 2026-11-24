{{-- resources/views/components/button.blade.php tombol utama putih dan varian tema --}}
@props(['href' => null, 'variant' => 'primary', 'type' => 'button'])
@php
    $classes = match ($variant) {
        'primary' => 'bg-white text-black bento-hover:bg-white/90 btn-press',
        'inverse' => 'bg-black text-white bento-hover:bg-black/80 btn-press',
        'success' => 'bg-green text-black bento-hover:bg-green/90 btn-press',
        'danger' => 'bg-pink text-black bento-hover:bg-pink/90 btn-press',
        'secondary' => 'border border-border-token text-white bento-hover:border-border-strong bento-hover:bg-white/5 btn-press',
        default => 'text-gray bento-hover:text-white',
    };
    $base = 'inline-flex min-h-11 items-center justify-center gap-2 rounded-btn px-4 py-2.5 text-caption font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white';
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$base $classes"]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "$base $classes disabled:cursor-not-allowed disabled:opacity-50"]) }}>{{ $slot }}</button>
@endif
