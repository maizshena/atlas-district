{{-- resources/views/components/back-link.blade.php tautan kembali konsisten antar halaman --}}
@props(['href'])

<a href="{{ $href }}" {{ $attributes->class('mb-4 inline-flex min-h-11 items-center gap-1.5 text-caption text-gray transition bento-hover:text-white') }}>
    <x-icon name="arrow-left" :size="16" /> {{ $slot }}
</a>