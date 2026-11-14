@extends('layouts.admin', ['title' => 'Venue'])

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-indigo-300">Master data</p>
            <h1 class="mt-1 text-3xl font-bold">Venue</h1>
        </div>
        <x-button :href="route('admin.venues.create')">Tambah venue</x-button>
    </div>

    <form method="GET" action="{{ route('admin.venues.index') }}" class="mb-4 flex max-w-md gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Cari venue" class="min-w-0 flex-1 rounded-xl border border-white/15 bg-slate-950 px-3.5 py-2.5 text-sm focus:border-indigo-400 focus:outline-none">
        <x-button type="submit" variant="secondary">Cari</x-button>
    </form>

    @if ($venues->isEmpty())
        <x-empty-state title="Belum ada venue" description="Tambahkan stadion atau gedung penyelenggara event." />
    @else
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Venue</th>
                        <th class="hidden px-4 py-3 sm:table-cell">Kota</th>
                        <th class="hidden px-4 py-3 md:table-cell">Kapasitas</th>
                        <th class="px-4 py-3">Event</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($venues as $venue)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($venue->image_url)
                                        <img src="{{ $venue->image_url }}" alt="" class="h-10 w-10 rounded-lg object-cover">
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $venue->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $venue->slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden px-4 py-3 text-slate-300 sm:table-cell">{{ $venue->city }}</td>
                            <td class="hidden px-4 py-3 text-slate-300 md:table-cell">{{ number_format($venue->capacity, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-slate-300">{{ $venue->events_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.venues.edit', $venue) }}" class="rounded-lg border border-white/15 px-3 py-1.5 text-xs hover:bg-white/10">Ubah</a>
                                    <form method="POST" action="{{ route('admin.venues.destroy', $venue) }}" onsubmit="return confirm('Hapus venue ini?');">
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
        <div class="mt-6">{{ $venues->links() }}</div>
    @endif
@endsection
