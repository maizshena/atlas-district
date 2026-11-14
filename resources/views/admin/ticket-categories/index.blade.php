@extends('layouts.admin', ['title' => 'Kategori '.$event->name])

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('admin.events.show', $event) }}" class="text-sm text-indigo-300 hover:text-indigo-200">&larr; {{ $event->name }}</a>
        <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
            <h1 class="text-3xl font-bold">Kategori tiket</h1>
            <x-button :href="route('admin.events.ticket-categories.create', $event)">Tambah kategori</x-button>
        </div>

        @if ($categories->isEmpty())
            <div class="mt-6">
                <x-empty-state title="Belum ada kategori tiket" description="Tambah kategori seperti Festival, VIP, atau Tribune." />
            </div>
        @else
            <div class="mt-6 overflow-hidden rounded-2xl border border-white/10 bg-slate-900">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Harga</th>
                            <th class="px-5 py-3">Terjual / Kuota</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-5 py-3 font-medium">{{ $category->name }}</td>
                                <td class="px-5 py-3 text-slate-300">{{ \App\Support\Formatters::rupiah($category->price) }}</td>
                                <td class="px-5 py-3 text-slate-300">{{ $category->sold }} / {{ $category->quota }}</td>
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
@endsection
