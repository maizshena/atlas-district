@extends('layouts.guest', ['title' => $event->name])

@section('content')
    @php
        $isClosed = $event->is_closed || $event->end_date->isPast();
        $maxPerOrder = (int) config('concert.max_tickets_per_order');
        $categoriesPayload = $event->ticketCategories->sortBy('price')->values()->map(fn ($category) => [
            'id' => $category->id,
            'price' => $category->price,
            'remaining' => max(0, $category->quota - $category->sold),
        ])->all();
        $startWib = \App\Support\Formatters::jakartaDateTime($event->start_date);
    @endphp

    <a href="{{ route('events.index') }}" class="mb-4 inline-flex min-h-11 items-center gap-1.5 text-caption text-gray transition hover:text-white">
        <x-icon name="arrow-left" :size="16" /> Semua konser
    </a>

    <x-bento.grid>
        <x-bento.tile span="4x2" class="relative overflow-hidden !p-0" aria-label="Poster {{ $event->name }}">
            <div class="relative h-full min-h-[300px]">
                @if ($event->banner_url)
                    <img src="{{ $event->banner_url }}" alt="Banner {{ $event->name }}" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover">
                @else
                    <div class="absolute inset-0 bg-linear-to-br from-surface-3 to-surface-2"></div>
                @endif
                <div class="absolute inset-0 bg-linear-to-t from-black via-black/50 to-black/10" aria-hidden="true"></div>
                <div class="relative flex h-full flex-col justify-end p-5 sm:p-7">
                    @if ($isClosed)
                        <x-chip tone="solid-pink" class="w-fit">Penjualan ditutup</x-chip>
                    @else
                        <x-chip tone="solid-green" class="w-fit">Tiket tersedia</x-chip>
                    @endif
                    <h1 class="mt-3 text-display text-white md:text-display-md">{{ $event->name }}</h1>
                </div>
            </div>
        </x-bento.tile>

        <x-bento.tile span="2x1" tone="raised">
            <p class="text-micro font-semibold uppercase tracking-wider text-gray">Tanggal</p>
            <p class="mt-2 flex items-center gap-2 text-heading font-semibold text-white">
                <x-icon name="calendar" :size="18" /> {{ $event->start_date->timezone('Asia/Jakarta')->translatedFormat('l, d F Y') }}
            </p>
            <p class="mt-1 text-caption text-gray"><span class="num text-white">{{ $startWib }}</span> WIB</p>
        </x-bento.tile>

        <x-bento.tile span="2x1">
            <p class="text-micro font-semibold uppercase tracking-wider text-gray">Lokasi</p>
            <p class="mt-2 flex items-center gap-2 text-heading font-semibold text-white">
                <x-icon name="pin" :size="18" /> {{ $event->venue->name }}
            </p>
            <p class="mt-1 text-caption text-gray">{{ $event->venue->address }}, {{ $event->venue->city }}</p>
        </x-bento.tile>

        <x-bento.tile span="2x1" tone="blue">
            <p class="text-micro font-semibold uppercase tracking-wider">Lineup</p>
            @if ($event->artists->isEmpty())
                <p class="mt-2 text-heading font-semibold">Segera diumumkan</p>
            @else
                <ul class="mt-2 flex flex-wrap gap-2">
                    @foreach ($event->artists as $artist)
                        <li class="inline-flex items-center gap-2 rounded-full bg-surface-1/80 py-1 pl-1 pr-3 text-caption font-medium text-white">
                            @if ($artist->photo_url)
                                <img src="{{ $artist->photo_url }}" alt="" loading="lazy" width="24" height="24" class="h-6 w-6 rounded-full object-cover">
                            @else
                                <span class="grid h-6 w-6 place-items-center rounded-full bg-white text-micro font-bold text-black">{{ mb_substr($artist->name, 0, 1) }}</span>
                            @endif
                            {{ $artist->name }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-bento.tile>

        <x-bento.tile span="2x2" tone="raised">
            <h2 class="text-heading font-semibold text-white">Tentang event</h2>
            <p class="mt-2 whitespace-pre-line text-caption leading-6 text-gray">{{ $event->description }}</p>
        </x-bento.tile>

        <x-bento.tile span="4x2" class="relative overflow-hidden !p-0">
            <div class="flex h-full flex-col p-4 lg:p-6" x-data="orderForm({{ \Illuminate\Support\Js::from($categoriesPayload) }}, {{ $maxPerOrder }})">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-heading font-semibold text-white">Pilih tiket</h2>
                    <span class="text-micro text-gray">Maks. <span class="num text-white">{{ $maxPerOrder }}</span> tiket per pesanan</span>
                </div>

                @if ($event->ticketCategories->isEmpty())
                    <div class="mt-4">
                        <x-empty-state icon="ticket" title="Kategori tiket belum tersedia" description="Pantau halaman ini untuk pembukaan penjualan." />
                    </div>
                @else
                    <form method="POST" action="{{ route('orders.store') }}" class="mt-4 space-y-2.5" @submit="submitting = true">
                        @csrf
                        @foreach ($event->ticketCategories->sortBy('price') as $category)
                            @php($remaining = max(0, $category->quota - $category->sold))
                            <div class="rounded-input border border-border-token bg-surface-2 p-3.5">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-body font-semibold text-white">{{ $category->name }}</p>
                                        <p class="num mt-0.5 text-caption text-white">{{ \App\Support\Formatters::rupiah($category->price) }}</p>
                                    </div>
                                    @if ($remaining > 0 && ! $isClosed)
                                        <div class="flex items-center gap-1.5">
                                            <button type="button" class="grid h-11 w-11 place-items-center rounded-btn border border-border-token text-heading text-white transition bento-hover:border-border-strong bento-hover:bg-white/5" @click="quantities[{{ $category->id }}] = Math.max(0, quantities[{{ $category->id }}] - 1)" aria-label="Kurangi tiket {{ $category->name }}">&minus;</button>
                                            <input type="number" min="0" max="{{ min($maxPerOrder, $remaining) }}" x-model.number="quantities[{{ $category->id }}]" @change="clamp({{ $category->id }})" class="num h-11 w-14 rounded-btn border border-border-token bg-surface-3 text-center text-caption text-white focus-visible:border-white" aria-label="Jumlah tiket {{ $category->name }}">
                                            <button type="button" class="grid h-11 w-11 place-items-center rounded-btn border border-border-token text-heading text-white transition bento-hover:border-border-strong bento-hover:bg-white/5" @click="quantities[{{ $category->id }}] = Math.min(quantities[{{ $category->id }}] + 1, {{ min($maxPerOrder, $remaining) }})" aria-label="Tambah tiket {{ $category->name }}">+</button>
                                        </div>
                                    @elseif ($remaining <= 0)
                                        <x-chip tone="solid-pink">Habis</x-chip>
                                    @else
                                        <x-chip tone="solid-gray">Ditutup</x-chip>
                                    @endif
                                </div>
                                <div class="mt-2 flex items-center justify-between gap-3 text-micro text-gray">
                                    <span>Sisa <span class="num text-white">{{ number_format($remaining, 0, ',', '.') }}</span> dari <span class="num">{{ number_format($category->quota, 0, ',', '.') }}</span></span>
                                    <template x-if="quantities[{{ $category->id }}] > 0">
                                        <span class="num text-white" x-text="format(prices[{{ $category->id }}] * quantities[{{ $category->id }}])"></span>
                                    </template>
                                </div>
                                <template x-if="quantities[{{ $category->id }}] > 0">
                                    <input type="hidden" name="items[{{ $loop->index }}][ticket_category_id]" value="{{ $category->id }}">
                                </template>
                                <template x-if="quantities[{{ $category->id }}] > 0">
                                    <input type="hidden" name="items[{{ $loop->index }}][quantity]" :value="quantities[{{ $category->id }}]">
                                </template>
                            </div>
                        @endforeach

                        <div class="mt-auto border-t border-border-token pt-4">
                            <div class="flex items-baseline justify-between gap-3">
                                <span class="text-caption text-gray">Total <span x-show="count() > 0" x-text="'(' + count() + ' tiket)'"></span></span>
                                <strong class="num text-title text-white" x-text="format(total())">Rp 0</strong>
                            </div>

                            @if ($isClosed)
                                <button type="button" disabled class="mt-3 flex min-h-11 w-full cursor-not-allowed items-center justify-center rounded-btn bg-surface-3 font-semibold text-gray">Penjualan ditutup</button>
                            @elseif (auth()->check())
                                <button type="submit" class="mt-3 flex min-h-11 w-full items-center justify-center rounded-btn bg-white px-4 font-bold text-black transition bento-hover:bg-white/90 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50" :disabled="submitting || count() < 1" x-text="submitting ? 'Membuat pesanan...' : 'Beli tiket'">Beli tiket</button>
                                <p class="mt-2 text-center text-micro text-gray">Kuota ditahan <span class="num">{{ config('concert.order_ttl_minutes') }}</span> menit sampai dibayar.</p>
                            @else
                                <x-button :href="route('login')" class="mt-3 w-full">Masuk untuk membeli</x-button>
                                <p class="mt-2 text-center text-micro text-gray">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-white underline underline-offset-4">Daftar gratis</a>.</p>
                            @endif
                        </div>
                    </form>
                @endif
            </div>
        </x-bento.tile>
    </x-bento.grid>
@endsection
