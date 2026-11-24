@extends('layouts.admin', ['title' => $artist->exists ? 'Ubah artis' : 'Tambah artis'])

@section('content')
    <x-admin.form-tile
        :back="route('admin.artists.index')"
        back-label="Artis"
        :title="$artist->exists ? 'Ubah artis' : 'Tambah artis'"
        description="Artis dapat ditautkan ke satu atau lebih event."
        :action="$artist->exists ? route('admin.artists.update', $artist) : route('admin.artists.store')"
        multipart
        :method="$artist->exists ? 'PUT' : null"
        :cancel="route('admin.artists.index')"
    >
        <x-input label="Nama artis" name="name" :value="$artist->name" required />
        <x-input label="Slug" name="slug" :value="$artist->slug" required />
        <x-textarea label="Bio" name="bio" rows="4">{{ $artist->bio }}</x-textarea>
        <x-admin.file-field label="Foto artis" name="photo" :preview="$artist->photo_url" preview-alt="Foto {{ $artist->name }}" />
    </x-admin.form-tile>
@endsection
