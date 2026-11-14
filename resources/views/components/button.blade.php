@props(['href' => null, 'variant' => 'primary', 'type' => 'button'])
@php
    $classes = match ($variant) {
        'primary' => 'bg-indigo-500 text-white hover:bg-indigo-400 focus-visible:outline-indigo-400',
        'success' => 'bg-emerald-500 text-slate-950 hover:bg-emerald-400 focus-visible:outline-emerald-400',
        'danger' => 'bg-rose-500/90 text-white hover:bg-rose-500 focus-visible:outline-rose-400',
        'secondary' => 'border border-white/15 text-slate-200 hover:bg-white/10 focus-visible:outline-white/40',
        default => 'text-slate-300 hover:text-white',
    };
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 $classes"]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60 $classes"]) }}>{{ $slot }}</button>
@endif
