{{-- resources/views/components/bento/media.blade.php isi tile gambar dengan lapisan gelap --}}
@props(['image' => null, 'alt' => '', 'eager' => false, 'overlay' => 'none', 'eyebrow' => null, 'title' => null, 'meta' => null])
@php($overlayTone = $overlay === 'none' ? null : "bg-$overlay/14 border border-$overlay/25")
<div class="relative h-full min-h-44 overflow-hidden rounded-[calc(var(--radius-tile)-0.35rem)] bg-surface-3">
    @if ($image)
        <img src="{{ $image }}" alt="{{ $alt }}" @if ($eager) fetchpriority="high" @else loading="lazy" @endif decoding="async" class="absolute inset-0 h-full w-full object-cover">
    @else
        <div class="absolute inset-0 grid place-items-center bg-linear-to-br from-surface-3 to-surface-0 text-gray">
            <span class="text-caption font-bold uppercase tracking-[0.2em]">Concert District</span>
        </div>
    @endif
    <div class="absolute inset-0 bg-linear-to-t from-scrim via-scrim/40 to-transparent" aria-hidden="true"></div>
    <div class="relative flex h-full flex-col justify-end gap-1.5 p-1 text-white">
        @if ($overlayTone)
            <span class="inline-flex w-fit items-center gap-1 rounded-full px-2.5 py-1 text-micro font-semibold text-black {{ $overlayTone }}">{{ $eyebrow ?? $overlay }}</span>
        @elseif ($eyebrow)
            <span class="inline-flex w-fit items-center gap-1 rounded-full bg-white px-2.5 py-1 text-micro font-semibold text-black">{{ $eyebrow }}</span>
        @endif
        @if ($title)
            <p class="text-heading font-bold leading-snug">{{ $title }}</p>
        @endif
        @if ($meta)
            <p class="text-micro text-white/80">{{ $meta }}</p>
        @endif
        {{ $slot }}
    </div>
</div>
