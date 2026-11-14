@extends('layouts.admin', ['title' => 'Pesanan'])

@section('content')
    <div class="mb-6">
        <p class="text-sm text-indigo-300">Transaksi</p>
        <h1 class="mt-1 text-3xl font-bold">Pesanan</h1>
    </div>

    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4 flex flex-wrap items-end gap-2">
        <input name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan" class="min-w-0 flex-1 rounded-xl border border-white/15 bg-slate-950 px-3.5 py-2.5 text-sm focus:border-indigo-400 focus:outline-none sm:max-w-xs">
        <select name="status" class="rounded-xl border border-white/15 bg-slate-950 px-3.5 py-2.5 text-sm">
            <option value="">Semua status</option>
            @foreach (\App\Enums\OrderStatus::cases() as $case)
                <option value="{{ $case->value }}" @selected($status === $case->value)>{{ $case->label() }}</option>
            @endforeach
        </select>
        <x-button type="submit" variant="secondary">Terapkan</x-button>
    </form>

    @if ($orders->isEmpty())
        <x-empty-state title="Tidak ada pesanan" description="Pesanan pelanggan akan muncul di sini." />
    @else
        <div class="overflow-x-auto rounded-2xl border border-white/10 bg-slate-900">
            <table class="w-full text-left text-sm">
                <thead class="bg-white/5 text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Pesanan</th>
                        <th class="hidden px-4 py-3 md:table-cell">Pelanggan</th>
                        <th class="hidden px-4 py-3 lg:table-cell">Item</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($orders as $order)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-mono font-medium">{{ $order->order_number }}</p>
                                <p class="text-xs text-slate-500">{{ \App\Support\Formatters::jakartaDateTime($order->created_at) }}</p>
                            </td>
                            <td class="hidden px-4 py-3 md:table-cell">
                                <p>{{ $order->user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $order->user->email }}</p>
                            </td>
                            <td class="hidden px-4 py-3 text-slate-300 lg:table-cell">
                                {{ $order->items->map(fn ($item) => $item->ticketCategory->name.' x'.$item->quantity)->join(', ') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 font-medium">{{ \App\Support\Formatters::rupiah($order->total_amount) }}</td>
                            <td class="px-4 py-3"><x-status-badge :status="$order->status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
@endsection
