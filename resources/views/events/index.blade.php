@extends('layouts.guest', ['title' => 'Konser resmi di Concert District'])

@section('content')
    <section class="mb-10 overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-indigo-600/25 via-slate-900 to-fuchsia-600/15 p-6 sm:p-10">
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-indigo-300">Konser resmi, tanpa calo</p>
        <h1 class="mt-3 max-w-2xl text-3xl font-bold leading-tight tracking-tight sm:text-5xl">Temukan panggung berikutnya.</h1>
        <p class="mt-3 max-w-xl text-slate-300">Pilih konser favorit, amankan tiket langsung, dan tunjukkan QR di pintu masuk.</p>

        <form method="GET" action="{{ route('events.index') }}" class="mt-7 flex max-w-xl gap-2" role="search">
            <input name="search" value="{{ $search }}" placeholder="Cari nama konser" aria-label="Cari nama konser" class="min-w-0 flex-1 rounded-xl border border-white/15 bg-slate-950/70 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-indigo-400 focus:outline-none">
            <x-button type="submit">Cari</x-button>
        </form>
        @if ($search !== '')
            <p class="mt-3 text-sm text-slate-400">Hasil untuk &ldquo;{{ $search }}&rdquo; &middot; <a href="{{ route('events.index') }}" class="text-indigo-300 hover:text-indigo-200">hapus pencarian</a></p>
        @endif
    </section>

    <section>
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm text-indigo-300">Jadwal pilihan</p>
                <h2 class="mt-1 text-2xl font-semibold">Konser mendatang</h2>
            </div>
            <span class="text-sm text-slate-400">{{ number_format($events->total(), 0, ',', '.') }} event</span>
        </div>

        @if ($events->isEmpty())
            <x-empty-state title="Belum ada konser yang cocok" description="Coba kata kunci lain atau kembali lagi nanti.">
                @if ($search !== '')
                    <x-button :href="route('events.index')" variant="secondary" class="mt-5">Lihat semua konser</x-button>
                @endif
            </x-empty-state>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($events as $event)
                    <x-event-card :event="$event" />
                @endforeach
            </div>
            <div class="mt-8">{{ $events->links() }}</div>
        @endif
    </section>
@endsection
