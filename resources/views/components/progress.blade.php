{{-- resources/views/components/progress.blade.php batang kuota dengan penanda penuh --}}
@props(['sold' => 0, 'quota' => 1])
@php($ratio = $quota > 0 ? min(100, round($sold / $quota * 100)) : 0)
<div class="h-1.5 w-full overflow-hidden rounded-full bg-white/10" role="img" aria-label="{{ $ratio }} persen kuota terjual">
    <div class="h-full rounded-full {{ $ratio >= 90 ? 'bg-pink' : 'bg-white' }}" style="width: {{ $ratio }}%"></div>
</div>
