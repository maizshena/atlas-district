@extends('layouts.admin', ['title' => 'Event'])

@section('content')
    <x-bento.page-header eyebrow="Katalog" title="Event" description="Kelola jadwal konser, banner, dan status penjualan.">
        <x-slot:actions>
            <x-button :href="route('admin.events.create')">Tambah event</x-button>
        </x-slot:actions>
    </x-bento.page-header>

    <x-admin.filter-bar :action="route('admin.events.index')" placeholder="Cari event" />

    @if ($events->isEmpty())
        <x-empty-state icon="calendar" title="Belum ada event" description="Buat event lalu tambahkan kategori tiketnya.">
            <x-button :href="route('admin.events.create')" class="mt-5">Tambah event</x-button>
        </x-empty-state>
    @else
        <x-admin.data-tile>
            <x-admin.table :head="[['label' => 'Event'], ['label' => 'Jadwal', 'hide' => 'md'], ['label' => 'Status'], ['label' => 'Kategori', 'hide' => 'sm'], ['label' => 'Aksi', 'align' => 'right']]">
                @foreach ($events as $event)
                    <tr class="transition hover:bg-surface-2">
                        <td class="px-4 py-3 lg:px-6">
                            <a href="{{ route('admin.events.show', $event) }}" class="font-semibold text-white hover:underline">{{ $event->name }}</a>
                            <p class="text-micro text-gray">{{ $event->venue->name }}@if (auth()->user()->isAdmin() && $event->organizer) · oleh {{ $event->organizer->name }}@endif</p>
                            <x-admin.event-status-chip :event="$event" class="mt-1" />
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-gray md:table-cell lg:px-6">{{ $event->start_date->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</td>
                        <td class="px-4 py-3 lg:px-6">
                            @if ($event->is_closed)
                                <x-chip tone="solid-gray"><x-icon name="cross" :size="12" /> Ditutup</x-chip>
                            @elseif ($event->end_date->isPast())
                                <x-chip tone="solid-pink"><x-icon name="clock" :size="12" /> Selesai</x-chip>
                            @else
                                <x-chip tone="solid-green"><x-icon name="check" :size="12" /> Aktif</x-chip>
                            @endif
                        </td>
                        <td class="hidden px-4 py-3 text-gray sm:table-cell lg:px-6">{{ $event->ticket_categories_count }}</td>
                        <td class="px-4 py-3 lg:px-6">
                            <div class="flex justify-end gap-2">
                                <x-admin.row-action :href="route('admin.events.edit', $event)">Ubah</x-admin.row-action>
                                @if (auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}">
                                        @csrf
                                        @method('DELETE')
                                        <x-admin.row-action tone="danger" confirm="Hapus event ini?">Hapus</x-admin.row-action>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        </x-admin.data-tile>
        <div class="mt-6">{{ $events->links() }}</div>
    @endif
@endsection
