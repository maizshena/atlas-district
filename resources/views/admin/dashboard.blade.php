@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <div class="mb-7 flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm text-indigo-300">Ringkasan operasional</p>
            <h1 class="mt-1 text-3xl font-bold">Dashboard</h1>
        </div>
        <x-button :href="route('admin.gate')" variant="secondary">Buka validasi gate</x-button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Event" :value="number_format($eventCount, 0, ',', '.')" hint="Seluruh event terdaftar" />
        <x-stat-card label="Tiket terjual" :value="number_format($soldCount, 0, ',', '.')" hint="Kuota terpakai termasuk pesanan menunggu bayar" />
        <x-stat-card label="Pendapatan" :value="\App\Support\Formatters::rupiah($revenue)" hint="Total pesanan lunas" />
        <x-stat-card label="Tiket dipindai" :value="number_format($scannedCount, 0, ',', '.')" hint="Validasi gate berhasil" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_320px]">
        <section class="rounded-2xl border border-white/10 bg-slate-900">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <h2 class="font-semibold">Pesanan terbaru</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-300 hover:text-indigo-200">Lihat semua</a>
            </div>
            @if ($recentOrders->isEmpty())
                <p class="px-5 py-8 text-center text-sm text-slate-400">Belum ada pesanan.</p>
            @else
                <ul class="divide-y divide-white/10">
                    @foreach ($recentOrders as $order)
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <div class="min-w-0">
                                <p class="truncate font-medium">{{ $order->order_number }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $order->user->name }} &middot; {{ $order->user->email }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <span class="hidden text-slate-300 sm:inline">{{ \App\Support\Formatters::rupiah($order->total_amount) }}</span>
                                <x-status-badge :status="$order->status" />
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <aside class="space-y-4">
            <div class="rounded-2xl border border-white/10 bg-slate-900 p-5">
                <p class="text-sm text-slate-400">Pesanan menunggu bayar</p>
                <p class="mt-1 text-3xl font-bold">{{ number_format($pendingCount, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">Dibatalkan otomatis setelah {{ config('concert.order_ttl_minutes') }} menit.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-slate-900 p-5">
                <h3 class="text-sm font-semibold">Pintasan</h3>
                <div class="mt-3 flex flex-col gap-2">
                    <x-button :href="route('admin.events.create')" variant="secondary" class="justify-start">Tambah event</x-button>
                    <x-button :href="route('admin.venues.create')" variant="secondary" class="justify-start">Tambah venue</x-button>
                    <x-button :href="route('admin.artists.create')" variant="secondary" class="justify-start">Tambah artis</x-button>
                </div>
            </div>
        </aside>
    </div>
@endsection
