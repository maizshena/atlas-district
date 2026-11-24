@extends('layouts.admin', ['title' => 'Kategori '.$event->name])

@section('content')
    <a href="{{ route('admin.events.show', $event) }}" class="mb-4 inline-flex min-h-11 items-center gap-1.5 text-caption text-gray hover:text-white">
        <x-icon name="arrow-left" :size="16" /> {{ $event->name }}
    </a>

    <x-bento.page-header eyebrow="Katalog tiket" title="Kategori tiket" :description="$event->name">
        <x-slot:actions>
            <x-button :href="route('admin.events.ticket-categories.create', $event)">Tambah kategori</x-button>
        </x-slot:actions>
    </x-bento.page-header>

    @if ($categories->isEmpty())
        <x-empty-state icon="ticket" title="Belum ada kategori tiket" description="Tambah kategori seperti Festival, VIP, atau Tribune.">
            <x-button :href="route('admin.events.ticket-categories.create', $event)" class="mt-5">Tambah kategori</x-button>
        </x-empty-state>
    @else
        <x-admin.data-tile>
            <x-admin.table :head="[['label' => 'Kategori'], ['label' => 'Harga'], ['label' => 'Terjual / Kuota'], ['label' => 'Aksi', 'align' => 'right']]">
                @foreach ($categories as $category)
                    <tr class="transition hover:bg-surface-2">
                        <td class="px-4 py-3 font-semibold text-white lg:px-6">{{ $category->name }}</td>
                        <td class="num px-4 py-3 text-white lg:px-6">{{ \App\Support\Formatters::rupiah($category->price) }}</td>
                        <td class="px-4 py-3 text-gray lg:px-6"><span class="num text-white">{{ $category->sold }}</span> / <span class="num">{{ $category->quota }}</span></td>
                        <td class="px-4 py-3 lg:px-6">
                            <div class="flex justify-end gap-2">
                                <x-admin.row-action :href="route('admin.ticket-categories.edit', $category)">Ubah</x-admin.row-action>
                                <form method="POST" action="{{ route('admin.ticket-categories.destroy', $category) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.row-action tone="danger" confirm="Hapus kategori tiket ini?">Hapus</x-admin.row-action>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        </x-admin.data-tile>
    @endif
@endsection
