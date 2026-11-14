@extends('layouts.admin', ['title' => 'Artis'])

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-indigo-300">Master data</p>
            <h1 class="mt-1 text-3xl font-bold">Artis</h1>
        </div>
        <x-button :href="route('admin.artists.create')">Tambah artis</x-button>
    </div>

    <form method="GET" action="{{ route('admin.artists.index') }}" class="mb-4 flex max-w-md gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Cari artis" class="min-w-0 flex-1 rounded-xl border border-white/15 bg-slate-950 px-3.5 py-2.5 text-sm focus:border-indigo-400 focus:outline-none">
        <x-button type="submit" variant="secondary">Cari</x-button>
    </form>

    @if ($artists->isEmpty())
        <x-empty-state title="Belum ada artis" description="Tambahkan artis untuk ditautkan ke event." />
    @else
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Artis</th>
                        <th class="hidden px-4 py-3 sm:table-cell">Bio</th>
                        <th class="px-4 py-3">Event</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($artists as $artist)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-full bg-indigo-500/20 text-sm font-semibold text-indigo-200">
                                        @if ($artist->photo_url)
                                            <img src="{{ $artist->photo_url }}" alt="" class="h-10 w-10 object-cover">
                                        @else
                                            {{ mb_substr($artist->name, 0, 1) }}
                                        @endif
                                    </span>
                                    <div>
                                        <p class="font-medium">{{ $artist->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $artist->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden max-w-xs truncate px-4 py-3 text-slate-400 sm:table-cell">{{ \Illuminate\Support\Str::limit($artist->bio, 60) ?: '-' }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $artist->events_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.artists.edit', $artist) }}" class="rounded-lg border border-white/15 px-3 py-1.5 text-xs hover:bg-white/10">Ubah</a>
                                    <form method="POST" action="{{ route('admin.artists.destroy', $artist) }}" onsubmit="return confirm('Hapus artis ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-rose-400/30 px-3 py-1.5 text-xs text-rose-300 hover:bg-rose-400/10">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $artists->links() }}</div>
    @endif
@endsection
