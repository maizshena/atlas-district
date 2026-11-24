@extends('layouts.guest', ['title' => 'Akses ditolak'])

@section('content')
    <div class="mx-auto w-full max-w-xl py-10">
        <x-bento.tile span="2x2" tone="pink" as="section" aria-label="Kesalahan 403">
            <div class="flex h-full flex-col justify-center">
                <span class="grid h-12 w-12 place-items-center rounded-full bg-white/70 text-black">
                    <x-icon name="cross" :size="22" />
                </span>
                <p class="num mt-4 text-display">{code}</p>
                <h1 class="mt-1 text-title">{title}</h1>
                <p class="mt-2 text-caption">{desc}</p>
                <x-button :href="route('events.index')" class="mt-6 w-fit">Kembali ke beranda</x-button>
            </div>
        </x-bento.tile>
    </div>
@endsection
