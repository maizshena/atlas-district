@props(['status'])
@php
    $value = $status instanceof \App\Enums\OrderStatus ? $status : \App\Enums\OrderStatus::tryFrom((string) $status);
    $label = $value?->label() ?? (string) $status;
    $classes = $value?->badgeClasses() ?? 'bg-slate-500/15 text-slate-300 ring-slate-400/30';
@endphp
<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset $classes"]) }}>{{ $label }}</span>
