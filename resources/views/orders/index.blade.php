@extends('layouts.guest', ['title' => 'Pesanan saya'])

@section('content')
    <x-bento.page-header eyebrow="Akun Anda" title="Pesanan saya" description="Riwayat reservasi tiket Anda di Concert District." />

    @if ($orders->isEmpty())
        <x-empty-state icon="ticket" title="Belum ada pesanan" description="Pilih konser lalu buat pesanan pertama Anda.">
            <x-button :href="route('events.index')" class="mt-5">Jelajahi konser</x-button>
        </x-empty-state>
    @else
        <x-bento.grid as="ul">
            @foreach ($orders as $order)
                <x-bento.tile span="2x1" :href="route('orders.show', $order)" :label="'Buka pesanan '.$order->order_number" as="li" class="list-none">
                    <div class="flex h-full flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="num text-caption text-white">{{ $order->order_number }}</p>
                            <p class="mt-1 truncate text-caption text-gray">{{ $order->items->pluck('ticketCategory.event.name')->unique()->join(', ') }}</p>
                            <p class="mt-1 text-micro text-gray">Dibuat {{ \App\Support\Formatters::jakartaDateTime($order->created_at) }}</p>
                        </div>
                        <div class="flex shrink-0 items-center justify-between gap-3 sm:flex-col sm:items-end">
                            <span class="num text-body text-white">{{ \App\Support\Formatters::rupiah($order->total_amount) }}</span>
                            <x-status-badge :status="$order->status" />
                        </div>
                    </div>
                </x-bento.tile>
            @endforeach
        </x-bento.grid>
        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
@endsection
