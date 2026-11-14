@extends('layouts.admin', ['title' => 'Event'])

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-indigo-300">Katalog</p>
            <h1 class="mt-1 text-3xl font-bold">Event</h1>
        </div>
        <x-button :href="route('admin.events.create')">Tambah event</x-button>
    </div>

    <form method="GET" action="{{ route('admin.events.index') }}" class="mb-4 flex max-w-md gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Cari event" class="min-w-0 flex-1 rounded-xl border border-white/15 bg-slate-950 px-3.5 py-2.5 text-sm focus:border-indigo-400 focus:outline-none">
        <x-button type="submit" variant="secondary">Cari</x-button>
    </form>

    @if ($events->isEmpty())
        <x-empty-state title="Belum ada event" description="Buat event lalu tambahkan kategori tiketnya." />
    @else
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Event</th>
                        <th class="hidden px-4 py-3 md:table-cell">Jadwal</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="hidden px-4 py-3 sm:table-cell">Kategori</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($events as $event)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.events.show', $event) }}" class="font-medium hover:text-indigo-300">{{ $event->name }}</a>
                                <p class="text-xs text-slate-500">{{ $event->venue->name }}</p>
                            </td>
                            <td class="hidden px-4 py-3 text-slate-300 md:table-cell">{{ $event->start_date->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                @if ($event->is_closed)
                                    <span class="rounded-full bg-slate-500/15 px-2.5 py-1 text-xs font-semibold text-slate-300">Ditutup</span>
                                @elseif ($event->end_date->isPast())
                                    <span class="rounded-full bg-rose-400/15 px-2.5 py-1 text-xs font-semibold text-rose-300">Selesai</span>
                                @else
                                    <span class="rounded-full bg-emerald-400/15 px-2.5 py-1 text-xs font-semibold text-emerald-300">Aktif</span>
                                @endif
                            </td>
                            <td class="hidden px-4 py-3 text-slate-300 sm:table-cell">{{ $event->ticket_categories_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.events.edit', $event) }}" class="rounded-lg border border-white/15 px-3 py-1.5 text-xs hover:bg-white/10">Ubah</a>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Hapus event ini?');">
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
        <div class="mt-6">{{ $events->links() }}</div>
    @endif
@endsection
