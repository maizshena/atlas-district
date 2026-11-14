@extends('layouts.admin', ['title' => $artist->exists ? 'Ubah artis' : 'Tambah artis'])

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.artists.index') }}" class="text-sm text-indigo-300 hover:text-indigo-200">&larr; Artis</a>
        <h1 class="mt-3 text-3xl font-bold">{{ $artist->exists ? 'Ubah artis' : 'Tambah artis' }}</h1>

        <form method="POST" action="{{ $artist->exists ? route('admin.artists.update', $artist) : route('admin.artists.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5 rounded-2xl border border-white/10 bg-slate-900 p-5 sm:p-6">
            @csrf
            @if ($artist->exists)
                @method('PUT')
            @endif

            <x-input label="Nama artis" name="name" :value="$artist->name" required />
            <x-input label="Slug" name="slug" :value="$artist->slug" required />
            <x-textarea label="Bio" name="bio" rows="4">{{ $artist->bio }}</x-textarea>

            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-300">Foto artis</span>
                @if ($artist->photo_url)
                    <img src="{{ $artist->photo_url }}" alt="Foto artis" class="mb-3 h-28 w-28 rounded-full object-cover">
                @endif
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-sm file:text-slate-200">
                <span class="mt-1 block text-xs text-slate-500">JPG, PNG, atau WEBP, maks 5 MB.</span>
                @error('photo')<span class="mt-1 block text-xs text-rose-300">{{ $message }}</span>@enderror
            </label>

            <div class="flex flex-wrap justify-end gap-3 border-t border-white/10 pt-5">
                <x-button :href="route('admin.artists.index')" variant="secondary">Batal</x-button>
                <x-button type="submit" data-submit>Simpan</x-button>
            </div>
        </form>
    </div>
@endsection
