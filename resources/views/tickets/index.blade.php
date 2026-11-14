@extends('layouts.guest', ['title' => 'Tiket saya'])

@section('content')
    <div class="mb-7">
        <p class="text-sm text-indigo-300">Akses masuk konser</p>
        <h1 class="mt-1 text-3xl font-bold">Tiket saya</h1>
    </div>

    @if ($pendingOrders->isNotEmpty())
        <section class="mb-8 rounded-2xl border border-amber-400/30 bg-amber-400/5 p-5">
            <h2 class="font-semibold text-amber-200">Pesanan menunggu pembayaran</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($pendingOrders as $order)
                    <li class="flex flex-wrap items-center justify-between gap-2">
                        <a href="{{ route('orders.show', $order) }}" class="truncate text-slate-200 hover:text-white">{{ $order->order_number }} &middot; {{ $order->items->pluck('ticketCategory.event.name')->unique()->join(', ') }}</a>
                        <span class="text-xs text-amber-200">Berakhir {{ \App\Support\Formatters::jakartaDateTime($order->expires_at) }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($groups->isEmpty())
        <x-empty-state title="Belum ada tiket" description="Tiket muncul di sini setelah pembayaran berhasil.">
            <x-button :href="route('events.index')" class="mt-5">Lihat konser</x-button>
        </x-empty-state>
    @else
        @foreach ($groups as $group)
            <section class="mb-8">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-sm text-slate-400">{{ $group['order']->order_number }}</p>
                        <h2 class="font-semibold">{{ $group['tickets']->first()->orderItem->ticketCategory->event->name }}</h2>
                    </div>
                    <span class="text-sm text-slate-400">{{ \App\Support\Formatters::jakartaDate($group['tickets']->first()->orderItem->ticketCategory->event->start_date) }}</span>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($group['tickets'] as $ticket)
                        <article class="flex gap-4 rounded-2xl border border-white/10 bg-slate-900 p-4">
                            <img src="{{ route('tickets.qr', $ticket) }}" alt="QR tiket {{ $ticket->ticket_code }}" loading="lazy" class="h-24 w-24 shrink-0 rounded-lg bg-white p-1 {{ $ticket->is_used ? 'opacity-40 grayscale' : '' }}">
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-wide text-slate-500">{{ $ticket->orderItem->ticketCategory->name }}</p>
                                <p class="mt-1 font-mono text-lg tracking-widest">{{ $ticket->ticket_code }}</p>
                                @if ($ticket->is_used)
                                    <p class="mt-2 inline-flex rounded-full bg-rose-400/15 px-2.5 py-1 text-xs font-semibold text-rose-300">Sudah digunakan {{ \App\Support\Formatters::jakartaDateTime($ticket->used_at) }}</p>
                                @else
                                    <p class="mt-2 inline-flex rounded-full bg-emerald-400/15 px-2.5 py-1 text-xs font-semibold text-emerald-300">Belum digunakan</p>
                                @endif
                                <p class="mt-2 text-xs text-slate-500">{{ $ticket->orderItem->ticketCategory->event->venue->name ?? '' }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
@endsection
