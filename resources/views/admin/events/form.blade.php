@extends('layouts.admin', ['title' => $event->exists ? 'Ubah event' : 'Tambah event'])

@section('content')
    @php
        $selectedArtists = collect(old('artist_ids', $event->exists ? $event->artists->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
        $toLocal = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->timezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '';
    @endphp

    <div class="mx-auto max-w-3xl">
        <a href="{{ route('admin.events.index') }}" class="text-sm text-indigo-300 hover:text-indigo-200">&larr; Event</a>
        <h1 class="mt-3 text-3xl font-bold">{{ $event->exists ? 'Ubah event' : 'Tambah event' }}</h1>

        <form method="POST" action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}" enctype="multipart/form-data" class="mt-6 space-y-5 rounded-2xl border border-white/10 bg-slate-900 p-5 sm:p-6" x-data="{ closed: {{ old('is_closed', $event->is_closed) ? 'true' : 'false' }} }">
            @csrf
            @if ($event->exists)
                @method('PUT')
            @endif

            <x-input label="Nama event" name="name" :value="$event->name" required />
            <x-input label="Slug" name="slug" :value="$event->slug" required />

            <x-select name="venue_id" label="Venue" required>
                <option value="">Pilih venue</option>
                @foreach ($venues as $venue)
                    <option value="{{ $venue->id }}" @selected((int) old('venue_id', $event->venue_id) === $venue->id)>{{ $venue->name }} · {{ $venue->city }}</option>
                @endforeach
            </x-select>

            <x-textarea label="Deskripsi" name="description" rows="5" required>{{ $event->description }}</x-textarea>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-input label="Mulai (WIB)" name="start_date" type="datetime-local" :value="$toLocal(old('start_date', $event->start_date))" required />
                <x-input label="Selesai (WIB)" name="end_date" type="datetime-local" :value="$toLocal(old('end_date', $event->end_date))" required />
            </div>

            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-slate-300">Banner event</span>
                @if ($event->banner_url)
                    <img src="{{ $event->banner_url }}" alt="Banner event" class="mb-3 h-36 w-full rounded-xl object-cover sm:w-72">
                @endif
                <input type="file" name="banner" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-sm file:text-slate-200">
                <span class="mt-1 block text-xs text-slate-500">Rasio 16:9, JPG/PNG/WEBP maks 5 MB.</span>
                @error('banner')<span class="mt-1 block text-xs text-rose-300">{{ $message }}</span>@enderror
            </label>

            <fieldset>
                <legend class="mb-2 text-sm font-medium text-slate-300">Artis yang tampil</legend>
                @if ($artists->isEmpty())
                    <p class="text-sm text-slate-500">Belum ada artis. <a href="{{ route('admin.artists.create') }}" class="text-indigo-300">Tambahkan artis</a>.</p>
                @else
                    <div class="grid max-h-56 gap-2 overflow-y-auto rounded-xl border border-white/10 p-3 sm:grid-cols-2">
                        @foreach ($artists as $option)
                            <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white/5">
                                <input type="checkbox" name="artist_ids[]" value="{{ $option->id }}" @checked(in_array($option->id, $selectedArtists, true)) class="h-4 w-4 rounded border-white/20 bg-slate-950 text-indigo-500">
                                {{ $option->name }}
                            </label>
                        @endforeach
                    </div>
                @endif
                @error('artist_ids')<span class="mt-1 block text-xs text-rose-300">{{ $message }}</span>@enderror
            </fieldset>

            <input type="hidden" name="is_closed" :value="closed ? 1 : 0">
            <label class="flex items-center gap-3 rounded-xl border border-white/10 p-4">
                <input type="checkbox" x-model="closed" class="h-4 w-4 rounded border-white/20 bg-slate-950 text-indigo-500">
                <span>
                    <span class="block text-sm font-medium">Tutup penjualan</span>
                    <span class="block text-xs text-slate-500">Pengunjung tidak dapat membuat pesanan baru untuk event ini.</span>
                </span>
            </label>

            <div class="flex flex-wrap justify-end gap-3 border-t border-white/10 pt-5">
                <x-button :href="route('admin.events.index')" variant="secondary">Batal</x-button>
                <x-button type="submit" data-submit>Simpan</x-button>
            </div>
        </form>
    </div>
@endsection
