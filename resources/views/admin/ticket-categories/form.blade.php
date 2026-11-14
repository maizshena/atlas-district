@extends('layouts.admin', ['title' => $category->exists ? 'Ubah kategori' : 'Tambah kategori'])

@section('content')
    <div class="mx-auto max-w-xl">
        <a href="{{ route('admin.events.show', $event) }}" class="text-sm text-indigo-300 hover:text-indigo-200">&larr; {{ $event->name }}</a>
        <h1 class="mt-3 text-3xl font-bold">{{ $category->exists ? 'Ubah kategori tiket' : 'Tambah kategori tiket' }}</h1>

        <form method="POST" action="{{ $category->exists ? route('admin.ticket-categories.update', $category) : route('admin.events.ticket-categories.store', $event) }}" class="mt-6 space-y-5 rounded-2xl border border-white/10 bg-slate-900 p-5 sm:p-6">
            @csrf
            @if ($category->exists)
                @method('PUT')
            @endif

            <x-input label="Nama kategori" name="name" :value="$category->name" required />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-input label="Harga (Rp)" name="price" type="number" min="0" step="1000" :value="$category->price" required />
                <x-input label="Kuota" name="quota" type="number" min="{{ max(1, $category->sold) }}" :value="$category->quota" required />
            </div>
            @if ($category->exists)
                <p class="-mt-2 text-xs text-slate-500">{{ $category->sold }} tiket sudah terjual. Kuota tidak boleh diturunkan di bawah angka itu.</p>
            @endif

            <div class="flex flex-wrap justify-end gap-3 border-t border-white/10 pt-5">
                <x-button :href="route('admin.events.show', $event)" variant="secondary">Batal</x-button>
                <x-button type="submit" data-submit>Simpan</x-button>
            </div>
        </form>
    </div>
@endsection
