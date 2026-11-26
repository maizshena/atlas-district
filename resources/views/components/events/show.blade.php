{{-- resources/views/events/show.blade.php halaman detail event tersusun dari komponen --}}
@extends('layouts.guest', ['title' => $event->name])

@section('content')
    @php
        $isClosed = $event->is_closed || $event->end_date->isPast();
        $maxPerOrder = (int) config('concert.max_tickets_per_order');
        $categories = $event->ticketCategories->sortBy('price')->values();
        $categoriesPayload = $categories->map(fn ($category) => [
            'id' => $category->id,
            'price' => $category->price,
            'remaining' => max(0, $category->quota - $category->sold),
        ])->all();
    @endphp

    <x-back-link :href="route('admin.events.index')">Event</x-back-link>

    <x-bento.grid>
        <x-event.banner :event="$event" :is-closed="$isClosed" :notice="$notice" />

        <x-event.fact-tile label="Tanggal" icon="calendar" :value="\App\Support\Formatters::jakartaDate($event->start_date)" tone="raised">
            <span class="num text-white">{{ \App\Support\Formatters::jakartaDateTime($event->start_date) }}</span>
        </x-event.fact-tile>

        <x-event.fact-tile label="Lokasi" icon="pin" :value="$event->venue->name">
            {{ $event->venue->address }}, {{ $event->venue->city }}
        </x-event.fact-tile>

        <x-bento.tile span="2x1" tone="blue">
            <x-bento.eyebrow :color="null">Lineup</x-bento.eyebrow>
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
            <div class="flex h-full flex-col p-4 lg:p-6" x-data="orderForm(@js($categoriesPayload), @js($maxPerOrder))">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-heading font-semibold text-white">Pilih tiket</h2>
                    <span class="text-micro text-gray">Maks. <span class="num text-white">{{ $maxPerOrder }}</span> tiket per pesanan</span>
                </div>

                @if ($categories->isEmpty())
                    <div class="mt-4">
                        <x-empty-state icon="ticket" title="Kategori tiket belum tersedia" description="Pantau halaman ini untuk pembukaan penjualan." />
                    </div>
                @else
                    <form method="POST" action="{{ route('orders.store') }}" class="mt-4 space-y-2.5" @submit="submitting = true">
                        @csrf
                        @foreach ($categories as $category)
                            <x-event.ticket-option :category="$category" :index="$loop->index" :max-per-order="$maxPerOrder" :is-closed="$isClosed" />
                        @endforeach
                        <x-event.checkout-summary :is-closed="$isClosed" />
                    </form>
                @endif
            </div>
        </x-bento.tile>
    </x-bento.grid>
@endsection