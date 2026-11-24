@extends('layouts.guest', ['title' => 'Pesanan '.$order->order_number])

@section('content')
    <a href="{{ route('orders.index') }}" class="mb-4 inline-flex min-h-11 items-center gap-1.5 text-caption text-gray transition hover:text-white">
        <x-icon name="arrow-left" :size="16" /> Pesanan saya
    </a>

    <x-bento.grid>
        <x-bento.tile span="3x2" tone="raised">
            <div class="flex h-full flex-col">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-micro font-semibold uppercase tracking-wider text-gray">Nomor pesanan</p>
                        <h1 class="num mt-1 text-title text-white">{{ $order->order_number }}</h1>
                        <p class="mt-1 text-micro text-gray">Dibuat {{ \App\Support\Formatters::jakartaDateTime($order->created_at) }}</p>
                    </div>
                    <x-status-badge :status="$order->status" />
                </div>

                @if ($order->status === \App\Enums\OrderStatus::Paid)
                    <div class="mt-5 flex items-center gap-3 rounded-input border border-green/30 bg-surface-1 p-4" role="status">
                        <span class="grid h-10 w-10 place-items-center rounded-full bg-green text-black animate-pop">
                            <x-icon name="check" :size="20" />
                        </span>
                        <div>
                            <p class="text-caption font-semibold text-green">Pembayaran berhasil</p>
                            <p class="text-micro text-gray">Dibayar {{ \App\Support\Formatters::jakartaDateTime($order->paid_at) }}. E-tiket juga dikirim ke email Anda.</p>
                        </div>
                    </div>
                @elseif ($order->status === \App\Enums\OrderStatus::Cancelled)
                    <p class="mt-5 flex items-center gap-2 text-caption text-gray"><x-icon name="cross" :size="16" /> Pesanan ini sudah dibatalkan. Kuota tiket telah dikembalikan.</p>
                @elseif ($order->status === \App\Enums\OrderStatus::Expired)
                    <p class="mt-5 flex items-center gap-2 text-caption text-gray"><x-icon name="clock" :size="16" /> Pesanan ini kedaluwarsa karena pembayaran tidak diselesaikan tepat waktu. Kuota tiket telah dikembalikan.</p>
                @endif

                <h2 class="mt-6 text-heading font-semibold text-white">Rincian tiket</h2>
                <ul class="mt-2 divide-y divide-border-token border-y border-border-token">
                    @foreach ($order->items as $item)
                        <li class="flex justify-between gap-4 py-3.5">
                            <div class="min-w-0">
                                <p class="text-caption font-medium text-white">{{ $item->ticketCategory->event->name }}</p>
                                <p class="text-micro text-gray">{{ $item->ticketCategory->name }} &middot; <span class="num">{{ $item->quantity }}</span> tiket &times; {{ \App\Support\Formatters::rupiah($item->unit_price) }}</p>
                            </div>
                            <p class="num whitespace-nowrap text-caption text-white">{{ \App\Support\Formatters::rupiah($item->unit_price * $item->quantity) }}</p>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-auto flex items-baseline justify-between gap-3 pt-5">
                    <span class="text-caption text-gray">Total</span>
                    <span class="num text-title text-white">{{ \App\Support\Formatters::rupiah($order->total_amount) }}</span>
                </div>
            </div>
        </x-bento.tile>

        <x-bento.tile span="3x2" class="flex flex-col">
            @if ($order->status === \App\Enums\OrderStatus::Pending && ! $order->expires_at->isPast())
                <div class="flex flex-col" x-data="countdown({{ $order->expires_at->timestamp * 1000 }})">
                    <p class="text-micro font-semibold uppercase tracking-wider text-orange">Batas pembayaran</p>
                    <p class="num countdown-tick mt-2 text-display text-orange" :class="ticking ? 'opacity-70' : 'opacity-100'" x-text="label()">{{ $order->expires_at->diffForHumans() }}</p>
                    <p class="mt-1 text-micro text-gray">{{ \App\Support\Formatters::jakartaDateTime($order->expires_at) }} &middot; halaman memuat ulang otomatis saat waktu habis.</p>
                </div>

                <div class="mt-auto space-y-2.5 pt-6">
                    <form method="POST" action="{{ route('orders.pay', $order) }}">
                        @csrf
                        <button type="submit" class="flex min-h-11 w-full items-center justify-center rounded-btn bg-white px-4 font-bold text-black transition bento-hover:bg-white/90 active:scale-[0.98]" data-submit>Bayar sekarang (simulasi)</button>
                    </form>
                    <form method="POST" action="{{ route('orders.cancel', $order) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="flex min-h-11 w-full items-center justify-center rounded-btn border border-border-token px-4 font-semibold text-white transition bento-hover:border-border-strong bento-hover:bg-white/5" data-submit>Batalkan pesanan</button>
                    </form>
                </div>
            @elseif ($order->status === \App\Enums\OrderStatus::Paid)
                <div class="flex flex-1 flex-col items-start justify-center">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-green text-black animate-pop">
                        <x-icon name="check" :size="24" />
                    </span>
                    <p class="mt-3 text-heading font-semibold text-white">Tiket siap dipakai</p>
                    <p class="mt-1 text-caption text-gray">Tunjukkan QR tiket di pintu masuk venue.</p>
                </div>
                <x-button :href="route('tickets.index')" class="mt-6 w-full">Lihat tiket saya</x-button>
            @else
                <div class="flex flex-1 flex-col items-start justify-center">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-pink text-black">
                        <x-icon name="cross" :size="24" />
                    </span>
                    <p class="mt-3 text-heading font-semibold text-white">Pesanan tidak aktif</p>
                    <p class="mt-1 text-caption text-gray">Buat pesanan baru dari halaman event.</p>
                </div>
                <x-button :href="route('events.index')" variant="secondary" class="mt-6 w-full">Cari konser lain</x-button>
            @endif
        </x-bento.tile>
    </x-bento.grid>
@endsection
