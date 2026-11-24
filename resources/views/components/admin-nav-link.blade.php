{{-- resources/views/components/admin-nav-link.blade.php tautan navigasi admin --}}
@props(['href', 'active' => false])
<a href="{{ $href }}" @if ($active) aria-current="page" @endif @class(['flex min-h-11 items-center gap-2 rounded-btn px-3 py-2 text-caption font-medium transition', 'bg-white text-black' => $active, 'text-gray bento-hover:bg-surface-2 bento-hover:text-white' => ! $active])>
    @isset($icon)
        {{ $icon }}
    @endisset
    {{ $slot }}
</a>
