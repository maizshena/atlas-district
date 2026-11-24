{{-- resources/views/admin/events/form.blade.php form event bersama dengan field terkunci untuk organizer --}}
@extends('layouts.admin', ['title' => $event->exists ? 'Ubah event' : 'Tambah event'])

@section('content')
    @php
        $isAdmin = auth()->user()->isAdmin();
        $infoOnly = ! $isAdmin && $event->exists;
        $selectedArtists = collect(old('artist_ids', $event->exists ? $event->artists->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
        $toLocal = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->timezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '';
    @endphp

    <x-admin.form-tile
        :back="route('admin.events.index')"
        back-label="Event"
        :title="$event->exists ? 'Ubah event' : 'Tambah event'"
        :description="$isAdmin ? 'Jadwal memakai zona waktu WIB.' : 'Event baru akan ditinjau admin sebelum tayang. Jadwal memakai zona waktu WIB.'"
        :action="$event->exists ? route('admin.events.update', $event) : route('admin.events.store')"
        multipart
        :method="$event->exists ? 'PUT' : null"
        :cancel="route('admin.events.index')"
        x-data="{ closed: @js((bool) old('is_closed', $event->is_closed)) }"
    >
        <x-input label="Nama event" name="name" :value="$event->name" required />

        @if ($infoOnly)
            <x-admin.locked-event-info :event="$event" />
        @else
            <x-input label="Slug" name="slug" :value="$event->slug" required />

            <x-select name="venue_id" label="Venue" required>
                <option value="">Pilih venue</option>
                @foreach ($venues as $venue)
                    <option value="{{ $venue->id }}" @selected((int) old('venue_id', $event->venue_id) === $venue->id)>{{ $venue->name }} · {{ $venue->city }}</option>
                @endforeach
            </x-select>
        @endif

        <x-textarea label="Deskripsi" name="description" rows="5" required>{{ $event->description }}</x-textarea>

        @unless ($infoOnly)
            <div class="grid gap-5 sm:grid-cols-2">
                <x-input label="Mulai (WIB)" name="start_date" type="datetime-local" :value="$toLocal(old('start_date', $event->start_date))" required />
                <x-input label="Selesai (WIB)" name="end_date" type="datetime-local" :value="$toLocal(old('end_date', $event->end_date))" required />
            </div>
        @endunless

        <x-admin.file-field label="Banner event" name="banner" :preview="$event->banner_url" preview-alt="Banner event" hint="Rasio 16:9, JPG/PNG/WEBP maks 5 MB." />

        @unless ($infoOnly)
            <fieldset>
                <legend class="mb-2 text-caption font-medium text-gray">Artis yang tampil</legend>
                @if ($artists->isEmpty())
                    <p class="text-caption text-gray">Belum ada artis. Hubungi admin untuk menambahkannya.</p>
                @else
                    <div class="grid max-h-56 gap-1 overflow-y-auto rounded-input border border-border-token bg-surface-1 p-2 sm:grid-cols-2">
                        @foreach ($artists as $option)
                            <label class="flex min-h-11 cursor-pointer items-center gap-2.5 rounded-btn px-2 text-caption text-white transition hover:bg-surface-2">
                                <input type="checkbox" name="artist_ids[]" value="{{ $option->id }}" @checked(in_array($option->id, $selectedArtists, true)) class="h-4 w-4 rounded border-border-strong bg-surface-3 accent-white">
                                {{ $option->name }}
                            </label>
                        @endforeach
                    </div>
                @endif
                @error('artist_ids')<span class="mt-1 block text-micro text-pink">{{ $message }}</span>@enderror
            </fieldset>
        @endunless

        @if ($isAdmin)
            <input type="hidden" name="is_closed" :value="closed ? 1 : 0">
            <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-input border border-border-token bg-surface-1 p-4">
                <input type="checkbox" x-model="closed" class="h-4 w-4 rounded border-border-strong bg-surface-3 accent-white">
                <span>
                    <span class="block text-caption font-medium text-white">Tutup penjualan</span>
                    <span class="block text-micro text-gray">Pengunjung tidak dapat membuat pesanan baru untuk event ini.</span>
                </span>
            </label>
        @endif
    </x-admin.form-tile>
@endsection