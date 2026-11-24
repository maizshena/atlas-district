@extends('layouts.admin', ['title' => $event->name])
@php($canManageCategories = auth()->user()->isAdmin())

@section('content')
    <a href="{{ route('admin.events.index') }}" class="mb-4 inline-flex min-h-11 items-center gap-1.5 text-caption text-gray hover:text-white">
        <x-icon name="arrow-left" :size="16" /> Event
    </a>

    <x-bento.page-header :title="$event->name" :description="$event->venue->name.' · '.\App\Support\Formatters::jakartaDateTime($event->start_date)">
        <x-slot:actions>
            <x-admin.event-review-actions :event="$event" />
            <x-button :href="route('admin.events.edit', $event)" variant="secondary">Ubah event</x-button>
            <x-button :href="route('events.show', $event->slug)">Lihat publik</x-button>
        </x-slot:actions>
    </x-bento.page-header>

    <x-bento.grid class="mb-6">
        <x-bento.tile span="2x1">
            <x-bento.stat label="Kategori tiket" :value="number_format($event->ticketCategories->count(), 0, ',', '.')" hint="Jenis tiket aktif" icon="ticket" />
        </x-bento.tile>
        <x-bento.tile span="2x1" tone="raised">
            <x-bento.stat label="Kuota terpakai" :value="number_format($event->ticketCategories->sum('sold'), 0, ',', '.').' / '.number_format($event->ticketCategories->sum('quota'), 0, ',', '.')" hint="Reservasi dan tiket lunas" icon="chart" />
        </x-bento.tile>
        <x-bento.tile span="2x1" :tone="$event->is_closed ? 'gray' : 'green'">
            <x-bento.stat label="Status penjualan" :value="$event->is_closed ? 'Ditutup' : ($event->end_date->isPast() ? 'Selesai' : 'Aktif')" :hint="$event->is_closed ? 'Penonton tidak dapat membuat pesanan' : 'Penonton dapat membuat pesanan'" />
        </x-bento.tile>
    </x-bento.grid>

    <div class="mb-4"><x-admin.event-status-chip :event="$event" /></div>

    @if ($event->ticketCategories->isEmpty())
        <x-empty-state icon="ticket" title="Belum ada kategori tiket" description="Tambahkan minimal satu agar event dapat dipesan.">
            <x-button :href="route('admin.events.ticket-categories.create', $event)" class="mt-5">Tambah kategori</x-button>
        </x-empty-state>
    @else
        <x-admin.data-tile title="Kategori tiket">
            <x-slot:actions>
                <x-button :href="route('admin.events.ticket-categories.create', $event)" variant="secondary">Tambah kategori</x-button>
            </x-slot:actions>
            <x-admin.table :head="[['label' => 'Kategori'], ['label' => 'Harga'], ['label' => 'Kuota'], ['label' => 'Terjual'], ['label' => 'Aksi', 'align' => 'right']]">
                @foreach ($event->ticketCategories->sortBy('price') as $category)
                    <tr class="transition hover:bg-surface-2">
                        <td class="px-4 py-3 font-semibold text-white lg:px-6">{{ $category->name }}</td>
                        <td class="num px-4 py-3 text-white lg:px-6">{{ \App\Support\Formatters::rupiah($category->price) }}</td>
                        <td class="num px-4 py-3 text-gray lg:px-6">{{ number_format($category->quota, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 lg:px-6">
                            <div class="flex items-center gap-3">
                                <span class="num w-14 text-white">{{ number_format($category->sold, 0, ',', '.') }}</span>
                                <div class="w-24"><x-progress :sold="$category->sold" :quota="$category->quota" /></div>
                            </div>
                        </td>
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
