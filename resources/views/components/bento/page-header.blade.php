{{-- resources/views/components/bento/page-header.blade.php judul halaman dan ajakan sekunder --}}
@props(['eyebrow' => null, 'title', 'description' => null])
<div {{ $attributes->merge(['class' => 'mb-6 flex flex-wrap items-end justify-between gap-4']) }}>
    <div class="min-w-0">
        @if ($eyebrow)
            <p class="text-micro font-semibold uppercase tracking-wider text-gray">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-1 text-display text-white">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 max-w-2xl text-caption text-gray">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap gap-2">{{ $actions }}</div>
    @endisset
</div>
