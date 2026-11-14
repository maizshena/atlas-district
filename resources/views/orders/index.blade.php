@extends('layouts.guest', ['title' => 'Pesanan saya'])

@section('content')
    <div class="mb-7">
        <p class="text-sm text-indigo-300">Akun Anda</p>
        <h1 class="mt-1 text-3xl font-bold">Pesanan saya</h1>
    </div>

    @if ($orders->isEmpty())
        <x-empty-state title="Belum ada pesanan" description="Pilih konser lalu buat pesanan pertama Anda.">
            <x-button :href="route('events.index')" class="mt-5">Jelajahi konser</x-button>
        </x-empty-state>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-slate-900 p-5 transition hover:border-indigo-400/50 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="font-semibold">{{ $order->order_number }}</p>
                        <p class="mt-1 truncate text-sm text-slate-400">{{ $order->items->pluck('ticketCategory.event.name')->unique()->join(', ') }}</p>
                        <p class="mt-1 text-xs text-slate-500">Dibuat {{ \App\Support\Formatters::jakartaDateTime($order->created_at) }}</p>
                    </div>
                    <div class="flex items-center gap-4 sm:flex-col sm:items-end">
                        <span class="font-semibold">{{ \App\Support\Formatters::rupiah($order->total_amount) }}</span>
                        <x-status-badge :status="$order->status" />
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $orders->links() }}</div>
    @endif
@endsection
