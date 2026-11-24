{{-- resources/views/components/status-badge.blade.php badge status dengan ikon dan teks --}}
@props(['status'])
@php
    $value = $status instanceof \App\Enums\OrderStatus ? $status : \App\Enums\OrderStatus::tryFrom((string) $status);
    $label = $value?->label() ?? (string) $status;
    $tone = $value?->tone() ?? 'neutral';
    $icon = $value?->icon() ?? 'dot';
@endphp
<x-chip :tone="$tone" {{ $attributes }}>
    <x-icon :name="$icon" :size="12" />
    {{ $label }}
</x-chip>
