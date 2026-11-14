@extends('layouts.guest', ['title' => 'Halaman tidak ditemukan'])

@section('content')
    <div class="mx-auto max-w-lg py-16 text-center">
        <p class="text-6xl font-black text-indigo-400">404</p>
        <h1 class="mt-4 text-2xl font-semibold">Halaman tidak ditemukan</h1>
        <p class="mt-2 text-slate-400">Alamat yang Anda buka tidak ada atau sudah dipindahkan.</p>
        <x-button :href="route('events.index')" class="mt-6">Kembali ke beranda</x-button>
    </div>
@endsection
