@extends('layouts.guest', ['title' => 'Konser resmi di Concert District'])

@section('content')
    @php
        $featured = $events->first();
        $nearest = $events->getCollection()->sortBy('start_date')->first();
    @endphp

    <x-bento.grid class="mb-6">
        @if ($featured)
            <x-bento.tile span="4x2" :href="route('events.show', $featured->slug)" :label="'Lihat detail '.$featured->name" class="relative overflow-hidden !p-0">
                <div class="relative h-full min-h-[320px]">
                    @if ($featured->banner_url)
                        <img src="{{ $featured->banner_url }}" alt="Banner {{ $featured->name }}" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover">
                    @else
                        <div class="absolute inset-0 bg-linear-to-br from-orange via-pink to-surface-3"></div>
                    @endif
                    <div class="absolute inset-0 bg-linear-to-t from-black via-black/55 to-black/10" aria-hidden="true"></div>
                    <div class="relative flex h-full flex-col justify-end p-5 sm:p-7">
                        <x-chip tone="solid-orange" class="w-fit">Event unggulan</x-chip>
                        <h1 class="mt-3 max-w-xl text-display text-white md:text-display-md">{{ $featured->name }}</h1>
                        <p class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-caption text-white/80">
                            <span class="inline-flex items-center gap-1.5"><x-icon name="calendar" :size="15" /> {{ $featured->start_date->timezone('Asia/Jakarta')->translatedFormat('l, d F Y · H:i') }} WIB</span>
                            <span class="inline-flex items-center gap-1.5"><x-icon name="pin" :size="15" /> {{ $featured->venue->name }}</span>
                        </p>
                    </div>
                </div>
            </x-bento.tile>
        @else
            <x-bento.tile span="4x2" class="relative overflow-hidden">
                <div class="flex h-full min-h-[280px] flex-col justify-end">
                    <p class="text-caption font-semibold uppercase tracking-wider text-gray">Concert District</p>
                    <h1 class="mt-2 max-w-xl text-display">Temukan panggung berikutnya.</h1>
                    <p class="mt-2 max-w-md text-caption text-gray">Belum ada konser terjadwal. Kembali lagi sebentar lagi.</p>
                </div>
            </x-bento.tile>
        @endif

        <x-bento.tile span="2x2" tone="raised" class="flex flex-col justify-between">
            <div>
                <p class="text-micro font-semibold uppercase tracking-wider text-gray">Event terdekat</p>
                @if ($nearest)
                    <p class="mt-2 text-heading font-semibold">{{ $nearest->name }}</p>
                    <p class="mt-1 text-micro text-gray">{{ $nearest->venue->name }} &middot; {{ $nearest->venue->city }}</p>
                    <div class="mt-4" x-data="countdown({{ $nearest->start_date->timestamp * 1000 }})">
                        <p class="text-micro text-gray">Dimulai dalam</p>
                        <p class="num countdown-tick text-display text-white" :class="ticking ? 'opacity-70' : 'opacity-100'" x-text="label()">{{ $nearest->start_date->diffForHumans() }}</p>
                    </div>
                @else
                    <p class="mt-2 text-caption text-gray">Belum ada jadwal terdekat.</p>
                @endif
            </div>
            <div class="mt-4 flex items-end justify-between gap-2 border-t border-border-token pt-3">
                <p class="text-micro text-gray">Total event tersedia</p>
                <p class="num text-title text-white">{{ number_format($events->total(), 0, ',', '.') }}</p>
            </div>
        </x-bento.tile>

        <x-bento.tile span="1x1" tone="white">
            <x-bento.stat value="{{ number_format($events->total(), 0, ',', '.') }}" label="Event tersedia" hint="Jadwal konser mendatang" />
        </x-bento.tile>

        <x-bento.tile span="1x1">
            <x-bento.action title="Cari konser" description="Nama artis atau event" :href="'#cari'" cta="Cari" />
        </x-bento.tile>
    </x-bento.grid>

    <x-bento.tile span="2x1" tone="raised" class="mb-8">
        <form method="GET" action="{{ route('events.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end" role="search" id="cari">
            <div class="flex-1">
                <x-input label="Cari nama konser" name="search" :value="$search" placeholder="Contoh: Festival Senja" />
            </div>
            <x-button type="submit" class="sm:mb-0.5">Cari</x-button>
        </form>
        @if ($search !== '')
            <p class="mt-3 text-caption text-gray">Hasil untuk &ldquo;{{ $search }}&rdquo; &middot; <a href="{{ route('events.index') }}" class="font-semibold text-white underline underline-offset-4">hapus pencarian</a></p>
        @endif
    </x-bento.tile>

    <section aria-labelledby="konser-mendatang" class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <h2 id="konser-mendatang" class="text-title text-white">Konser mendatang</h2>
        <span class="text-caption text-gray"><span class="num text-white">{{ number_format($events->total(), 0, ',', '.') }}</span> event</span>
    </section>

    @if ($events->isEmpty())
        <x-empty-state icon="search" title="Belum ada konser yang cocok" description="Coba kata kunci lain atau kembali lagi nanti.">
            @if ($search !== '')
                <x-button :href="route('events.index')" variant="secondary" class="mt-5">Lihat semua konser</x-button>
            @endif
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:gap-4 lg:grid-cols-3">
            @foreach ($events as $event)
                <x-event-card :event="$event" />
            @endforeach
        </div>
        <div class="mt-8">{{ $events->links() }}</div>
    @endif
@endsection
