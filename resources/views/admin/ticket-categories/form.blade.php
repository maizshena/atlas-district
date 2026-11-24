@extends('layouts.admin', ['title' => $category->exists ? 'Ubah kategori' : 'Tambah kategori'])

@section('content')
    <x-admin.form-tile
        :back="route('admin.events.show', $event)"
        :back-label="$event->name"
        :title="$category->exists ? 'Ubah kategori tiket' : 'Tambah kategori tiket'"
        :description="$event->name"
        :action="$category->exists ? route('admin.ticket-categories.update', $category) : route('admin.events.ticket-categories.store', $event)"
        :method="$category->exists ? 'PUT' : null"
        :cancel="route('admin.events.show', $event)"
    >
        <x-input label="Nama kategori" name="name" :value="$category->name" required />
        <div class="grid gap-5 sm:grid-cols-2">
            <x-input label="Harga (Rp)" name="price" type="number" min="0" step="1000" :value="$category->price" required />
            <x-input label="Kuota" name="quota" type="number" min="{{ max(1, $category->sold) }}" :value="$category->quota" :hint="$category->exists ? $category->sold.' tiket sudah terjual. Kuota tidak boleh diturunkan di bawah angka itu.' : null" required />
        </div>
    </x-admin.form-tile>
@endsection
