{{-- resources/views/components/event/banner.blade.php tile banner event dengan strip pengumuman di dasar --}}
@props(['event', 'isClosed', 'notice' => null])

<x-bento.tile span="4x2" class="relative flex flex-col overflow-hidden !p-0" aria-label="Poster {{ $event->name }}">
    <div class="relative min-h-[300px] flex-1">
        @if ($event->banner_url)
            <img src="{{ $event->banner_url }}" alt="Banner {{ $event->name }}" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover">
        @else
            <div class="absolute inset-0 bg-linear-to-br from-surface-3 to-surface-2"></div>
        @endif
        <div class="absolute inset-0 bg-linear-to-t from-black via-black/50 to-black/10" aria-hidden="true"></div>
        <div class="relative flex h-full flex-col justify-end p-5 sm:p-7">
            @if ($isClosed)
                <x-chip tone="solid-pink" class="w-fit">Penjualan ditutup</x-chip>
            @else
                <x-chip tone="solid-green" class="w-fit">Tiket tersedia</x-chip>
            @endif
            <h1 class="mt-3 text-display text-white md:text-display-md">{{ $event->name }}</h1>
        </div>
    </div>
    @if ($notice)
        <x-event.update-notice :notice="$notice" />
    @endif
</x-bento.tile>