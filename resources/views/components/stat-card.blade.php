@props(['label', 'value', 'hint' => null])
<div class="rounded-2xl border border-white/10 bg-slate-900 p-5">
    <p class="text-sm text-slate-400">{{ $label }}</p>
    <p class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
