@extends('layouts.guest', ['title' => 'Terjadi kesalahan'])

@section('content')
    <div class="mx-auto max-w-lg py-16 text-center">
        <p class="text-6xl font-black text-amber-400">500</p>
        <h1 class="mt-4 text-2xl font-semibold">Terjadi kesalahan</h1>
        <p class="mt-2 text-slate-400">Server gagal memproses permintaan. Coba beberapa saat lagi.</p>
        <x-button :href="route('events.index')" class="mt-6">Kembali ke beranda</x-button>
    </div>
@endsection
