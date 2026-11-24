@extends('layouts.admin', ['title' => 'Artis'])

@section('content')
    <x-bento.page-header eyebrow="Master data" title="Artis" description="Lineup yang tampil di event konser.">
        <x-slot:actions>
            <x-button :href="route('admin.artists.create')">Tambah artis</x-button>
        </x-slot:actions>
    </x-bento.page-header>

    <x-admin.filter-bar :action="route('admin.artists.index')" placeholder="Cari artis" />

    @if ($artists->isEmpty())
        <x-empty-state icon="music" title="Belum ada artis" description="Tambahkan artis untuk ditautkan ke event.">
            <x-button :href="route('admin.artists.create')" class="mt-5">Tambah artis</x-button>
        </x-empty-state>
    @else
        <x-admin.data-tile>
            <x-admin.table :head="[['label' => 'Artis'], ['label' => 'Bio', 'hide' => 'sm'], ['label' => 'Event'], ['label' => 'Aksi', 'align' => 'right']]">
                @foreach ($artists as $artist)
                    <tr class="transition hover:bg-surface-2">
                        <td class="px-4 py-3 lg:px-6">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-full bg-surface-3 text-caption font-semibold text-white">
                                    @if ($artist->photo_url)
                                        <img src="{{ $artist->photo_url }}" alt="" loading="lazy" width="40" height="40" class="h-10 w-10 object-cover">
                                    @else
                                        {{ mb_substr($artist->name, 0, 1) }}
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <p class="font-semibold text-white">{{ $artist->name }}</p>
                                    <p class="truncate text-micro text-gray">{{ $artist->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden max-w-xs truncate px-4 py-3 text-gray sm:table-cell lg:px-6">{{ \Illuminate\Support\Str::limit($artist->bio, 60) ?: '-' }}</td>
                        <td class="px-4 py-3 text-gray lg:px-6"><span class="num">{{ $artist->events_count }}</span></td>
                        <td class="px-4 py-3 lg:px-6">
                            <div class="flex justify-end gap-2">
                                <x-admin.row-action :href="route('admin.artists.edit', $artist)">Ubah</x-admin.row-action>
                                <form method="POST" action="{{ route('admin.artists.destroy', $artist) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.row-action tone="danger" confirm="Hapus artis ini?">Hapus</x-admin.row-action>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        </x-admin.data-tile>
        <div class="mt-6">{{ $artists->links() }}</div>
    @endif
@endsection
