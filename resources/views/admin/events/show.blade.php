@extends('layouts.admin', ['title' => $event->name])

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('admin.events.index') }}" class="text-sm text-indigo-300 hover:text-indigo-200">&larr; Event</a>

        <div class="mt-3 flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-3xl font-bold">{{ $event->name }}</h1>
                <p class="mt-1 text-sm text-slate-400">{{ $event->venue->name }} &middot; {{ \App\Support\Formatters::jakartaDateTime($event->start_date) }}</p>
            </div>
            <div class="flex gap-2">
                <x-button :href="route('admin.events.edit', $event)" variant="secondary">Ubah event</x-button>
                <x-button :href="route('events.show', $event->slug)" variant="secondary">Lihat publik</x-button>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-white/10 bg-slate-900">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 px-5 py-4">
                <h2 class="font-semibold">Kategori tiket</h2>
                <x-button :href="route('admin.events.ticket-categories.create', $event)">Tambah kategori</x-button>
            </div>
            @if ($event->ticketCategories->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-slate-400">Belum ada kategori tiket. Tambahkan minimal satu agar event dapat dipesan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-5 py-3">Kategori</th>
                                <th class="px-5 py-3">Harga</th>
                                <th class="px-5 py-3">Kuota</th>
                                <th class="px-5 py-3">Terjual</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach ($event->ticketCategories->sortBy('price') as $category)
                                <tr>
                                    <td class="px-5 py-3 font-medium">{{ $category->name }}</td>
                                    <td class="px-5 py-3 text-slate-300">{{ \App\Support\Formatters::rupiah($category->price) }}</td>
                                    <td class="px-5 py-3 text-slate-300">{{ number_format($category->quota, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-16 text-slate-300">{{ number_format($category->sold, 0, ',', '.') }}</span>
                                            <div class="w-24"><x-progress :sold="$category->sold" :quota="$category->quota" /></div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.ticket-categories.edit', $category) }}" class="rounded-lg border border-white/15 px-3 py-1.5 text-xs hover:bg-white/10">Ubah</a>
                                            <form method="POST" action="{{ route('admin.ticket-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori tiket ini?');">
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
            @endif
        </div>
    </div>
@endsection
