{{-- resources/views/components/event/ticket-option.blade.php baris satu kategori tiket di form pembelian --}}
@props(['category', 'index', 'maxPerOrder', 'isClosed'])

@php
    $remaining = max(0, $category->quota - $category->sold);
    $limit = min($maxPerOrder, $remaining);
@endphp

<div class="rounded-input border border-border-token bg-surface-2 p-3.5">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-body font-semibold text-white">{{ $category->name }}</p>
            <p class="num mt-0.5 text-caption text-white">{{ \App\Support\Formatters::rupiah($category->price) }}</p>
        </div>
        @if ($remaining > 0 && ! $isClosed)
            <x-event.quantity-stepper :category-id="$category->id" :name="$category->name" :limit="$limit" />
        @elseif ($remaining <= 0)
            <x-chip tone="solid-pink">Habis</x-chip>
        @else
            <x-chip tone="solid-gray">Ditutup</x-chip>
        @endif
    </div>
    <div class="mt-2 flex items-center justify-between gap-3 text-micro text-gray">
        <span>Sisa <span class="num text-white">{{ number_format($remaining, 0, ',', '.') }}</span> dari <span class="num">{{ number_format($category->quota, 0, ',', '.') }}</span></span>
        <template x-if="quantities[@js($category->id)] > 0">
            <span class="num text-white" x-text="format(prices[@js($category->id)] * quantities[@js($category->id)])"></span>
        </template>
    </div>
    <template x-if="quantities[@js($category->id)] > 0">
        <span class="contents">
            <input type="hidden" name="items[{{ $index }}][ticket_category_id]" value="{{ $category->id }}">
            <input type="hidden" name="items[{{ $index }}][quantity]" :value="quantities[@js($category->id)]">
        </span>
    </template>
</div>