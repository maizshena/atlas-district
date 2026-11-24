@props(['href' => null])

@php
    $classes = 'flex min-h-11 w-full items-center rounded-btn px-3 text-left text-caption text-gray transition bento-hover:bg-surface-2 bento-hover:text-white';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif