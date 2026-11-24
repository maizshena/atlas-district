{{-- resources/views/components/empty-state.blade.php keadaan kosong dengan satu ajakan --}}
@props(['title', 'description' => null, 'icon' => 'ticket'])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center rounded-tile border border-dashed border-border-token bg-surface-1 px-6 py-12 text-center']) }}>
    <span class="grid h-12 w-12 place-items-center rounded-full bg-surface-3 text-white">
        <x-icon :name="$icon" :size="22" />
    </span>
    <p class="mt-4 text-heading text-white">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-caption text-gray">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
