{{-- resources/views/components/bento/action.blade.php isi tile ajakan utama --}}
@props(['title', 'description' => null, 'href' => null, 'cta' => null])
<div class="flex h-full flex-col">
    <p class="text-heading font-bold leading-snug">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 text-caption sub">{{ $description }}</p>
    @endif
    @isset($icon)
        <div class="mt-3 opacity-80">{{ $icon }}</div>
    @endisset
    @if ($href && $cta)
        <a href="{{ $href }}" class="mt-auto inline-flex min-h-11 w-fit items-center gap-2 rounded-btn bg-white px-4 py-2.5 text-caption font-bold text-black transition bento-hover:bg-white/85 active:scale-[0.98]">
            {{ $cta }}
            <x-icon name="arrow-right" :size="16" />
        </a>
    @endif
    {{ $slot }}
</div>
