{{-- resources/views/components/admin/row-action.blade.php tombol aksi kecil dalam baris tabel --}}
@props(['href' => null, 'tone' => 'neutral', 'confirm' => null])
@php
    $classes = $tone === 'danger'
        ? 'border-pink/40 text-pink bento-hover:bg-pink/10'
        : 'border-border-token text-white bento-hover:border-border-strong bento-hover:bg-white/5';
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex min-h-9 items-center rounded-btn border px-3 text-micro font-semibold transition $classes"]) }}>{{ $slot }}</a>
@else
    <button type="submit" @if ($confirm) onclick="return confirm('{{ $confirm }}')" @endif {{ $attributes->merge(['class' => "inline-flex min-h-9 items-center rounded-btn border px-3 text-micro font-semibold transition $classes"]) }}>{{ $slot }}</button>
@endif
