@props(['title', 'description' => null])
<div class="rounded-2xl border border-dashed border-white/15 bg-slate-900/50 px-6 py-12 text-center">
    <p class="text-base font-medium text-slate-200">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 text-sm text-slate-400">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
