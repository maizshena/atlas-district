{{-- resources/views/components/admin/filter-bar.blade.php tile pencarian ringkas untuk halaman admin --}}
@props(['action', 'name' => 'search', 'placeholder' => 'Cari data'])
<x-bento.tile span="2x1" tone="raised" class="mb-4">
    <form method="GET" action="{{ $action }}" class="flex flex-col gap-2 sm:flex-row sm:items-end" role="search">
        <input name="{{ $name }}" value="{{ request($name) }}" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}" class="h-11 min-w-0 flex-1 rounded-input border border-border-token bg-surface-3 px-3.5 text-caption text-white placeholder:text-gray focus:outline-none focus-visible:border-white">
        {{ $slot }}
        <x-button type="submit" variant="secondary">Cari</x-button>
    </form>
</x-bento.tile>
