@extends('layouts.admin', ['title' => $venue->exists ? 'Ubah venue' : 'Tambah venue'])

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.venues.index') }}" class="text-sm text-indigo-300 hover:text-indigo-200">&larr; Venue</a>
        <h1 class="mt-3 text-3xl font-bold">{{ $venue->exists ? 'Ubah venue' : 'Tambah venue' }}</h1>

        <form method="POST" action="{{ $venue->exists ? route('admin.venues.update', $venue) : route('admin.venues.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5 rounded-2xl border border-white/10 bg-slate-900 p-5 sm:p-6">
            @csrf
            @if ($venue->exists)
                @method('PUT')
            @endif

            <x-input label="Nama venue" name="name" :value="$venue->name" required />
            <x-input label="Slug" name="slug" :value="$venue->slug" required />
            <x-textarea label="Alamat" name="address" rows="3" required>{{ $venue->address }}</x-textarea>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-input label="Kota" name="city" :value="$venue->city" required />
                <x-input label="Kapasitas" name="capacity" type="number" min="1" :value="$venue->capacity" required />
            </div>

            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-300">Gambar venue</span>
                @if ($venue->image_url)
                    <img src="{{ $venue->image_url }}" alt="Gambar venue" class="mb-3 h-32 w-full rounded-xl object-cover sm:w-64">
                @endif
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-sm file:text-slate-200">
                <span class="mt-1 block text-xs text-slate-500">JPG, PNG, atau WEBP, maks 5 MB.</span>
                @error('image')<span class="mt-1 block text-xs text-rose-300">{{ $message }}</span>@enderror
            </label>

            <div class="flex flex-wrap justify-end gap-3 border-t border-white/10 pt-5">
                <x-button :href="route('admin.venues.index')" variant="secondary">Batal</x-button>
                <x-button type="submit" data-submit>Simpan</x-button>
            </div>
        </form>
    </div>
@endsection
