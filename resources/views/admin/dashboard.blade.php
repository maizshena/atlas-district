@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <x-bento.page-header eyebrow="Ringkasan operasional" title="Dashboard" description="Pantau penjualan, pembayaran, dan pemakaian tiket.">
        <x-slot:actions>
            <x-button :href="route('admin.gate')" variant="secondary">Buka validasi gate</x-button>
        </x-slot:actions>
    </x-bento.page-header>

    <x-bento.grid>
        <x-bento.tile span="2x1" tone="white">
            <x-bento.stat label="Pendapatan" :value="\App\Support\Formatters::rupiah($revenue)" hint="Total pesanan lunas" icon="chart" />
        </x-bento.tile>

        <x-bento.tile span="1x1">
            <x-bento.stat label="Pesanan menunggu" :value="number_format($pendingCount, 0, ',', '.')" :count="$pendingCount" :hint="'Dibatalkan otomatis '.config('concert.order_ttl_minutes').' menit'" icon="clock" />
        </x-bento.tile>

        <x-bento.tile span="1x1" tone="raised">
            <x-bento.stat label="Tiket terjual" :value="number_format($soldCount, 0, ',', '.')" :count="$soldCount" hint="Termasuk pesanan menunggu bayar" icon="ticket" />
        </x-bento.tile>

        <x-bento.tile span="1x1">
            <x-bento.stat label="Event terdaftar" :value="number_format($eventCount, 0, ',', '.')" :count="$eventCount" hint="Seluruh event" icon="calendar" />
        </x-bento.tile>

        <x-bento.tile span="1x1" tone="blue">
            <x-bento.stat label="Tiket dipindai" :value="number_format($scannedCount, 0, ',', '.')" :count="$scannedCount" hint="Validasi gate berhasil" icon="scan" />
        </x-bento.tile>

        <x-bento.tile span="4x2">
            <x-bento.list :items="$recentOrders" title="Pesanan terbaru" empty="Belum ada pesanan.">
                <x-slot:action>
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex min-h-11 items-center text-caption font-semibold text-gray hover:text-white">Lihat semua</a>
                </x-slot:action>
                @foreach ($recentOrders as $order)
                    <li class="flex items-center justify-between gap-3 py-2.5 text-caption">
                        <div class="min-w-0">
                            <p class="num truncate text-white">{{ $order->order_number }}</p>
                            <p class="truncate text-micro text-gray">{{ $order->user->name }} &middot; {{ $order->user->email }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span class="num hidden text-white sm:inline">{{ \App\Support\Formatters::rupiah($order->total_amount) }}</span>
                            <x-status-badge :status="$order->status" />
                        </div>
                    </li>
                @endforeach
            </x-bento.list>
        </x-bento.tile>

    </x-bento.grid>
@endsection
