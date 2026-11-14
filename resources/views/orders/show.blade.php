@extends('layouts.guest', ['title' => 'Pesanan '.$order->order_number])

@section('content')
    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-300 hover:text-indigo-200">&larr; Pesanan saya</a>

    <section class="mx-auto mt-5 max-w-3xl rounded-2xl border border-white/10 bg-slate-900 p-5 sm:p-8">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
            <div>
                <p class="text-sm text-indigo-300">Nomor pesanan</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight">{{ $order->order_number }}</h1>
                <p class="mt-1 text-sm text-slate-400">Dibuat {{ \App\Support\Formatters::jakartaDateTime($order->created_at) }}</p>
            </div>
            <x-status-badge :status="$order->status" class="w-fit" />
        </div>

        @if ($order->status === \App\Enums\OrderStatus::Pending)
            <div class="mt-5 rounded-xl border border-amber-400/30 bg-amber-400/10 p-4" x-data="countdown({{ $order->expires_at->timestamp * 1000 }})">
                <p class="text-sm text-amber-200">Selesaikan pembayaran sebelum batas waktu.</p>
                <p class="mt-2 font-mono text-2xl font-bold text-amber-100" x-text="label()">{{ $order->expires_at->diffForHumans() }}</p>
                <p class="mt-1 text-xs text-amber-200/80">{{ \App\Support\Formatters::jakartaDateTime($order->expires_at) }} &middot; halaman memuat ulang otomatis saat waktu habis.</p>
            </div>
        @elseif ($order->status === \App\Enums\OrderStatus::Paid)
            <p class="mt-5 text-sm text-emerald-300">Dibayar pada {{ \App\Support\Formatters::jakartaDateTime($order->paid_at) }}. E-tiket juga dikirim ke email Anda.</p>
        @endif

        <div class="mt-6 divide-y divide-white/10 border-y border-white/10">
            @foreach ($order->items as $item)
                <div class="flex justify-between gap-4 py-4">
                    <div class="min-w-0">
                        <p class="font-medium">{{ $item->ticketCategory->event->name }}</p>
                        <p class="text-sm text-slate-400">{{ $item->ticketCategory->name }} &middot; {{ $item->quantity }} tiket &times; {{ \App\Support\Formatters::rupiah($item->unit_price) }}</p>
                    </div>
                    <p class="whitespace-nowrap font-medium">{{ \App\Support\Formatters::rupiah($item->unit_price * $item->quantity) }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-5 flex justify-between text-lg font-semibold">
            <span>Total</span>
            <span>{{ \App\Support\Formatters::rupiah($order->total_amount) }}</span>
        </div>

        @if ($order->status === \App\Enums\OrderStatus::Pending && ! $order->expires_at->isPast())
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <form method="POST" action="{{ route('orders.pay', $order) }}" class="flex-1">
                    @csrf
                    <x-button type="submit" variant="success" class="w-full" data-submit>Bayar sekarang (simulasi)</x-button>
                </form>
                <form method="POST" action="{{ route('orders.cancel', $order) }}" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <x-button type="submit" variant="secondary" class="w-full" data-submit>Batalkan pesanan</x-button>
                </form>
            </div>
        @elseif ($order->status === \App\Enums\OrderStatus::Paid)
            <div class="mt-6">
                <x-button :href="route('tickets.index')" variant="secondary">Lihat tiket saya</x-button>
            </div>
        @elseif ($order->status === \App\Enums\OrderStatus::Cancelled)
            <p class="mt-6 text-sm text-slate-400">Pesanan ini sudah dibatalkan. Kuota tiket telah dikembalikan.</p>
        @elseif ($order->status === \App\Enums\OrderStatus::Expired)
            <p class="mt-6 text-sm text-slate-400">Pesanan ini kedaluwarsa karena pembayaran tidak diselesaikan tepat waktu. Kuota tiket telah dikembalikan.</p>
        @endif
    </section>
@endsection
