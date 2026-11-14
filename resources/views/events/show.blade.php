@extends('layouts.guest', ['title' => $event->name])

@section('content')
    @php
        $now = now();
        $isClosed = $event->is_closed || $event->end_date->isPast();
        $maxPerOrder = (int) config('concert.max_tickets_per_order');
        $categoriesPayload = $event->ticketCategories->sortBy('price')->values()->map(fn ($category) => [
            'id' => $category->id,
            'price' => $category->price,
            'remaining' => max(0, $category->quota - $category->sold),
        ])->all();
    @endphp

    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-300 hover:text-indigo-200">&larr; Semua konser</a>

    <section class="mt-5 overflow-hidden rounded-3xl border border-white/10 bg-slate-900">
        <div class="aspect-[21/9] max-h-[420px] bg-slate-800">
            @if ($event->banner_url)
                <img src="{{ $event->banner_url }}" alt="Banner {{ $event->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full items-center justify-center bg-gradient-to-br from-indigo-900 via-slate-900 to-fuchsia-900 text-6xl font-bold text-white/60">CD</div>
            @endif
        </div>

        <div class="grid gap-8 p-5 sm:p-8 lg:grid-cols-[1fr_380px]">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-300">{{ $event->start_date->timezone('Asia/Jakarta')->translatedFormat('l, d F Y · H:i') }} WIB</p>
                    @if ($isClosed)
                        <span class="rounded-full bg-rose-400/15 px-2.5 py-1 text-xs font-semibold text-rose-300 ring-1 ring-inset ring-rose-400/30">Event ditutup</span>
                    @endif
                </div>
                <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ $event->name }}</h1>
                <p class="mt-4 whitespace-pre-line leading-7 text-slate-300">{{ $event->description }}</p>

                <div class="mt-7 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Lokasi</p>
                        <p class="mt-1 font-medium">{{ $event->venue->name }}</p>
                        <p class="text-sm text-slate-400">{{ $event->venue->address }}, {{ $event->venue->city }}</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-slate-950/60 p-4">
                        <p class="text-xs uppercase tracking-wide text-slate-500">Artis</p>
                        @if ($event->artists->isEmpty())
                            <p class="mt-1 font-medium">Segera diumumkan</p>
                        @else
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($event->artists as $artist)
                                    <span class="inline-flex items-center gap-2 rounded-full bg-white/5 py-1 pl-1 pr-3 text-sm">
                                        @if ($artist->photo_url)
                                            <img src="{{ $artist->photo_url }}" alt="" class="h-6 w-6 rounded-full object-cover">
                                        @endif
                                        {{ $artist->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-slate-950/60 p-5" x-data="orderForm({{ \Illuminate\Support\Js::from($categoriesPayload) }}, {{ $maxPerOrder }})">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Pilih tiket</h2>
                    <span class="text-xs text-slate-500">Maks. {{ $maxPerOrder }} tiket</span>
                </div>

                @if ($event->ticketCategories->isEmpty())
                    <p class="mt-4 rounded-xl border border-dashed border-white/15 p-4 text-sm text-slate-400">Kategori tiket belum tersedia.</p>
                @else
                    <form method="POST" action="{{ route('orders.store') }}" class="mt-4 space-y-3" @submit="submitting = true">
                        @csrf
                        @foreach ($event->ticketCategories->sortBy('price') as $category)
                            @php($remaining = max(0, $category->quota - $category->sold))
                            <div class="rounded-xl border border-white/10 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-medium">{{ $category->name }}</p>
                                        <p class="mt-0.5 text-sm font-semibold text-emerald-300">{{ \App\Support\Formatters::rupiah($category->price) }}</p>
                                    </div>
                                    @if ($remaining > 0 && ! $isClosed)
                                        <div class="flex items-center gap-1">
                                            <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-white/15 text-lg hover:bg-white/10" @click="quantities[{{ $category->id }}] = Math.max(0, quantities[{{ $category->id }}] - 1)" aria-label="Kurangi tiket {{ $category->name }}">&minus;</button>
                                            <input type="number" min="0" max="{{ min($maxPerOrder, $remaining) }}" x-model.number="quantities[{{ $category->id }}]" @change="clamp({{ $category->id }})" class="w-14 rounded-lg border border-white/15 bg-slate-900 px-1 py-1.5 text-center text-sm" aria-label="Jumlah tiket {{ $category->name }}">
                                            <button type="button" class="grid h-8 w-8 place-items-center rounded-lg border border-white/15 text-lg hover:bg-white/10" @click="quantities[{{ $category->id }}] = Math.min(quantities[{{ $category->id }}] + 1, {{ min($maxPerOrder, $remaining) }})" aria-label="Tambah tiket {{ $category->name }}">+</button>
                                        </div>
                                    @elseif ($remaining <= 0)
                                        <span class="rounded-full bg-rose-400/15 px-2.5 py-1 text-xs font-semibold text-rose-300">Habis</span>
                                    @else
                                        <span class="text-xs text-slate-500">Ditutup</span>
                                    @endif
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                    <span>Sisa {{ number_format($remaining, 0, ',', '.') }} dari {{ number_format($category->quota, 0, ',', '.') }}</span>
                                    <template x-if="quantities[{{ $category->id }}] > 0">
                                        <span class="font-medium text-slate-300" x-text="format(prices[{{ $category->id }}] * quantities[{{ $category->id }}])"></span>
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

                        <div class="flex items-center justify-between border-t border-white/10 pt-4 text-sm">
                            <span class="text-slate-400">Total <span x-show="count() > 0" x-text="'(' + count() + ' tiket)'"></span></span>
                            <strong x-text="format(total())">Rp 0</strong>
                        </div>

                        @if ($isClosed)
                            <button type="button" disabled class="w-full cursor-not-allowed rounded-xl bg-slate-800 px-4 py-3 font-semibold text-slate-500">Event ditutup</button>
                        @elseif (auth()->check())
                            <button type="submit" class="w-full rounded-xl bg-indigo-500 px-4 py-3 font-semibold hover:bg-indigo-400 disabled:opacity-60" :disabled="submitting || count() < 1" x-text="submitting ? 'Membuat pesanan...' : 'Buat pesanan'">Buat pesanan</button>
                            <p class="text-center text-xs text-slate-500">Kuota ditahan selama {{ config('concert.order_ttl_minutes') }} menit sampai dibayar.</p>
                        @else
                            <x-button :href="route('login')" class="w-full">Masuk untuk membeli</x-button>
                        @endif
                        @guest
                            <p class="text-center text-xs text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="text-indigo-300">Daftar gratis</a>.</p>
                        @endguest
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
