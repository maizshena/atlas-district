@extends('layouts.guest', ['title' => 'Akses ditolak'])

@section('content')
    <div class="mx-auto max-w-lg py-16 text-center">
        <p class="text-6xl font-black text-rose-400">403</p>
        <h1 class="mt-4 text-2xl font-semibold">Akses ditolak</h1>
        <p class="mt-2 text-slate-400">Akun Anda tidak memiliki izin untuk membuka halaman ini.</p>
        <x-button :href="route('events.index')" class="mt-6">Kembali ke beranda</x-button>
    </div>
@endsection
