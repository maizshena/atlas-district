@extends('layouts.admin', ['title' => 'Pesanan'])

@section('content')
    <x-bento.page-header eyebrow="Transaksi" title="Pesanan" description="Seluruh pesanan dengan status pembayaran dan pelanggannya." />

    <x-admin.filter-bar :action="route('admin.orders.index')" placeholder="Cari nomor pesanan">
        <select name="status" aria-label="Filter status" class="h-11 rounded-input border border-border-token bg-surface-3 px-3 text-caption text-white focus:outline-none focus-visible:border-white">
            <option value="">Semua status</option>
            @foreach (\App\Enums\OrderStatus::cases() as $case)
                <option value="{{ $case->value }}" @selected($status === $case->value)>{{ $case->label() }}</option>
            @endforeach
        </select>
    </x-admin.filter-bar>

    @if ($orders->isEmpty())
        <x-empty-state icon="ticket" title="Tidak ada pesanan" description="Pesanan pelanggan akan muncul di sini." />
    @else
        <x-admin.data-tile>
            <x-admin.table :head="[['label' => 'Pesanan'], ['label' => 'Pelanggan', 'hide' => 'md'], ['label' => 'Item', 'hide' => 'lg'], ['label' => 'Total'], ['label' => 'Status']]">
                @foreach ($orders as $order)
                    <tr class="transition hover:bg-surface-2">
                        <td class="px-4 py-3 lg:px-6">
                            <p class="num font-semibold text-white">{{ $order->order_number }}</p>
                            <p class="text-micro text-gray">{{ \App\Support\Formatters::jakartaDateTime($order->created_at) }}</p>
                        </td>
                        <td class="hidden px-4 py-3 md:table-cell lg:px-6">
                            <p class="text-white">{{ $order->user->name }}</p>
                            <p class="text-micro text-gray">{{ $order->user->email }}</p>
                        </td>
                        <td class="hidden px-4 py-3 text-gray lg:table-cell lg:px-6">
                            {{ $order->items->map(fn ($item) => $item->ticketCategory->name.' x'.$item->quantity)->join(', ') }}
                        </td>
                        <td class="num whitespace-nowrap px-4 py-3 text-white lg:px-6">{{ \App\Support\Formatters::rupiah($order->total_amount) }}</td>
                        <td class="px-4 py-3 lg:px-6"><x-status-badge :status="$order->status" /></td>
                    </tr>
                @endforeach
            </x-admin.table>
        </x-admin.data-tile>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
@endsection
