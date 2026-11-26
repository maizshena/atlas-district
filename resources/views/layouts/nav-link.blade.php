{{-- resources/views/components/layout/nav-link.blade.php tautan teks navigasi utama --}}
@props(['href'])

<a href="{{ $href }}" {{ $attributes->class('min-h-11 items-center rounded-btn px-3 text-gray transition bento-hover:text-white') }}>{{ $slot }}</a>