@extends('layouts.admin', ['title' => 'Venue'])

@section('content')
    <x-bento.page-header eyebrow="Master data" title="Venue" description="Stadion dan gedung penyelenggara konser.">
        <x-slot:actions>
            <x-button :href="route('admin.venues.create')">Tambah venue</x-button>
        </x-slot:actions>
    </x-bento.page-header>

    <x-admin.filter-bar :action="route('admin.venues.index')" placeholder="Cari venue" />

    @if ($venues->isEmpty())
        <x-empty-state icon="pin" title="Belum ada venue" description="Tambahkan stadion atau gedung penyelenggara event.">
            <x-button :href="route('admin.venues.create')" class="mt-5">Tambah venue</x-button>
        </x-empty-state>
    @else
        <x-admin.data-tile>
            <x-admin.table :head="[['label' => 'Venue'], ['label' => 'Kota', 'hide' => 'sm'], ['label' => 'Kapasitas', 'hide' => 'md'], ['label' => 'Event'], ['label' => 'Aksi', 'align' => 'right']]">
                @foreach ($venues as $venue)
                    <tr class="transition hover:bg-surface-2">
                        <td class="px-4 py-3 lg:px-6">
                            <div class="flex items-center gap-3">
                                @if ($venue->image_url)
                                    <img src="{{ $venue->image_url }}" alt="" loading="lazy" width="40" height="40" class="h-10 w-10 rounded-btn object-cover">
                                @else
                                    <span class="grid h-10 w-10 place-items-center rounded-btn bg-surface-3 text-gray"><x-icon name="pin" :size="16" /></span>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-white">{{ $venue->name }}</p>
                                    <p class="truncate text-micro text-gray">{{ $venue->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden px-4 py-3 text-gray sm:table-cell lg:px-6">{{ $venue->city }}</td>
                        <td class="hidden px-4 py-3 text-gray md:table-cell lg:px-6"><span class="num">{{ number_format($venue->capacity, 0, ',', '.') }}</span></td>
                        <td class="px-4 py-3 text-gray lg:px-6"><span class="num">{{ $venue->events_count }}</span></td>
                        <td class="px-4 py-3 lg:px-6">
                            <div class="flex justify-end gap-2">
                                <x-admin.row-action :href="route('admin.venues.edit', $venue)">Ubah</x-admin.row-action>
                                <form method="POST" action="{{ route('admin.venues.destroy', $venue) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.row-action tone="danger" confirm="Hapus venue ini?">Hapus</x-admin.row-action>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        </x-admin.data-tile>
        <div class="mt-6">{{ $venues->links() }}</div>
    @endif
@endsection
