@extends('layouts.guest', ['title' => 'Tiket saya'])

@section('content')
    <x-bento.page-header eyebrow="Akses masuk konser" title="Tiket saya" description="Tunjukkan QR berikut di pintu masuk. Setiap tiket hanya dapat dipindai satu kali." />

    @if ($pendingOrders->isNotEmpty())
        <x-bento.tile span="6x2" tone="orange" class="mb-6">
            <h2 class="text-heading font-semibold">Menunggu pembayaran</h2>
            <ul class="mt-3 divide-y divide-border-token">
                @foreach ($pendingOrders as $order)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 text-caption">
                        <a href="{{ route('orders.show', $order) }}" class="truncate font-medium underline underline-offset-4">{{ $order->order_number }} &middot; {{ $order->items->pluck('ticketCategory.event.name')->unique()->join(', ') }}</a>
                        <span class="inline-flex items-center gap-1.5 text-micro font-semibold"><x-icon name="clock" :size="14" /> Berakhir {{ \App\Support\Formatters::jakartaDateTime($order->expires_at) }}</span>
                    </li>
                @endforeach
            </ul>
        </x-bento.tile>
    @endif

    @if ($groups->isEmpty())
        <x-empty-state icon="ticket" title="Belum ada tiket" description="Tiket muncul di sini setelah pembayaran berhasil.">
            <x-button :href="route('events.index')" class="mt-5">Lihat konser</x-button>
        </x-empty-state>
    @else
        @foreach ($groups as $group)
            @php($event = $group['tickets']->first()->orderItem->ticketCategory->event)
            <section class="mb-6" aria-labelledby="grup-{{ $group['order']->id }}">
                <div class="mb-3 flex flex-wrap items-end justify-between gap-2">
                    <div>
                        <p class="text-micro text-gray">{{ $group['order']->order_number }}</p>
                        <h2 id="grup-{{ $group['order']->id }}" class="text-title text-white">{{ $event->name }}</h2>
                    </div>
                    <p class="text-caption text-gray">{{ \App\Support\Formatters::jakartaDate($event->start_date) }} &middot; {{ $event->venue->name ?? '' }}</p>
                </div>

                <x-bento.grid as="ul">
                    @foreach ($group['tickets'] as $ticket)
                        <x-bento.tile span="3x2" as="li" tone="blue" class="!p-3 text-white">
                            <div class="flex h-full flex-col gap-3">
                                <div class="rounded-btn bg-white p-2 animate-reveal">
                                    <img src="{{ route('tickets.qr', $ticket) }}" alt="QR tiket {{ $ticket->ticket_code }}" loading="lazy" width="280" height="280" class="mx-auto aspect-square w-full max-w-[220px] object-contain {{ $ticket->is_used ? 'opacity-40 grayscale' : '' }}">
                                </div>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="text-micro font-semibold uppercase tracking-wider">{{ $ticket->orderItem->ticketCategory->name }}</p>
                                        @if ($ticket->is_used)
                                            <x-chip tone="solid-pink"><x-icon name="cross" :size="12" /> Sudah digunakan</x-chip>
                                        @else
                                            <x-chip tone="solid-green"><x-icon name="check" :size="12" /> Belum digunakan</x-chip>
                                        @endif
                                    </div>
                                    <p class="num mt-1 break-all text-heading text-white">{{ $ticket->ticket_code }}</p>
                                    @if ($ticket->is_used)
                                        <p class="mt-1 text-micro text-gray">Dipakai {{ \App\Support\Formatters::jakartaDateTime($ticket->used_at) }}</p>
                                    @endif
                                </div>
                            </div>
                        </x-bento.tile>
                    @endforeach
                </x-bento.grid>
            </section>
        @endforeach
    @endif
@endsection
