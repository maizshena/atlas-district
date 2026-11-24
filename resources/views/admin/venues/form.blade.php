@extends('layouts.admin', ['title' => $venue->exists ? 'Ubah venue' : 'Tambah venue'])

@section('content')
    <x-admin.form-tile
        :back="route('admin.venues.index')"
        back-label="Venue"
        :title="$venue->exists ? 'Ubah venue' : 'Tambah venue'"
        description="Data venue dipakai saat membuat event konser."
        :action="$venue->exists ? route('admin.venues.update', $venue) : route('admin.venues.store')"
        multipart
        :method="$venue->exists ? 'PUT' : null"
        :cancel="route('admin.venues.index')"
    >
        <x-input label="Nama venue" name="name" :value="$venue->name" required />
        <x-input label="Slug" name="slug" :value="$venue->slug" hint="Dipakai pada alamat halaman publik venue." required />
        <x-textarea label="Alamat" name="address" rows="3" required>{{ $venue->address }}</x-textarea>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-input label="Kota" name="city" :value="$venue->city" required />
            <x-input label="Kapasitas" name="capacity" type="number" min="1" :value="$venue->capacity" required />
        </div>
        <x-admin.file-field label="Gambar venue" name="image" :preview="$venue->image_url" preview-alt="Gambar venue" />
    </x-admin.form-tile>
@endsection
