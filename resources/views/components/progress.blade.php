@props(['sold' => 0, 'quota' => 1])
@php($ratio = $quota > 0 ? min(100, round($sold / $quota * 100)) : 0)
<div class="h-1.5 w-full overflow-hidden rounded-full bg-white/10">
    <div class="h-full rounded-full {{ $ratio >= 90 ? 'bg-rose-400' : 'bg-indigo-400' }}" style="width: {{ $ratio }}%"></div>
</div>
