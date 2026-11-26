{{-- resources/views/components/gate/result-card.blade.php kartu hasil validasi gate --}}
@props(['tone', 'icon', 'title', 'role' => 'alert'])

<x-bento.tile span="4x2" :tone="$tone" as="section" :role="$role" {{ $attributes }}>
    <div class="flex items-start gap-3">
        <x-bento.icon-badge :icon="$icon" />
        <div class="min-w-0">
            <h2 class="text-title">{{ $title }}</h2>
            {{ $slot }}
        </div>
    </div>
    {{ $footer ?? '' }}
</x-bento.tile>