@extends('layouts.admin', ['title' => 'Validasi gate'])

@section('content')
    <div class="mx-auto max-w-xl">
        <div class="mb-6">
            <p class="text-sm text-indigo-300">Pintu masuk</p>
            <h1 class="mt-1 text-3xl font-bold">Validasi tiket</h1>
            <p class="mt-2 text-sm text-slate-400">Pilih event, klik kolom kode tiket, lalu pindai barcode atau ketik kode manual.</p>
        </div>

        <form method="POST" action="{{ route('admin.gate.validate') }}" class="rounded-2xl border border-white/10 bg-slate-900 p-5 sm:p-6">
            @csrf

            <label for="event_id" class="text-sm font-medium text-slate-300">Event</label>
            <select id="event_id" name="event_id" required
                class="mt-2 w-full rounded-xl border border-white/15 bg-slate-950 px-4 py-3 text-white focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                <option value="">Pilih event yang dijaga</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}" @selected((int) old('event_id', $selectedEventId) === $event->id)>
                        {{ $event->name }} ({{ $event->start_date->format('d M Y') }})
                    </option>
                @endforeach
            </select>
            @error('event_id')
                <p class="mt-1.5 text-xs text-rose-300">{{ $message }}</p>
            @enderror

            <label for="ticket_code" class="mt-5 block text-sm font-medium text-slate-300">Kode tiket</label>
            <input id="ticket_code" name="ticket_code" value="{{ old('ticket_code') }}" autocomplete="off" inputmode="numeric" placeholder="0000-0000-0000" maxlength="14" required
                class="mt-2 w-full rounded-xl border border-white/15 bg-slate-950 px-4 py-4 text-center font-mono text-2xl tracking-[0.3em] text-white placeholder:text-slate-600 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
            @error('ticket_code')
                <p class="mt-1.5 text-xs text-rose-300">{{ $message }}</p>
            @enderror
            <x-button type="submit" class="mt-4 w-full" data-submit>Validasi tiket</x-button>
        </form>

        @if (session('gate_result'))
            @php($result = session('gate_result'))
            <p class="mt-4 text-xs text-slate-500">Gate: {{ session('gate_event_name') }}</p>
            @if ($result['result'] === 'valid')
                @php($ticket = $result['ticket'])
                <div class="mt-5 rounded-2xl border border-emerald-400/40 bg-emerald-400/10 p-6 text-emerald-200" role="status">
                    <p class="text-2xl font-bold">Tiket valid</p>
                    <p class="mt-2 font-mono text-lg tracking-widest">{{ $ticket->ticket_code }}</p>
                    <dl class="mt-4 space-y-1 text-sm">
                        <div class="flex justify-between gap-4"><dt>Event</dt><dd class="text-right font-medium">{{ $ticket->orderItem->ticketCategory->event->name }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Kategori</dt><dd class="font-medium">{{ $ticket->orderItem->ticketCategory->name }}</dd></div>
                        <div class="flex justify-between gap-4"><dt>Pesanan</dt><dd class="font-mono">{{ $ticket->order->order_number }}</dd></div>
                    </dl>
                </div>
            @elseif ($result['result'] === 'used')
                <div class="mt-5 rounded-2xl border border-rose-400/40 bg-rose-400/10 p-6 text-rose-200" role="alert">
                    <p class="text-2xl font-bold">Tiket sudah digunakan</p>
                    <p class="mt-2 text-sm">Waktu pemakaian: {{ $result['used_at'] }}</p>
                </div>
            @elseif ($result['result'] === 'wrong_event')
                <div class="mt-5 rounded-2xl border border-rose-400/40 bg-rose-400/10 p-6 text-rose-200" role="alert">
                    <p class="text-2xl font-bold">Tiket bukan untuk event ini</p>
                    <p class="mt-2 text-sm">Tiket milik: <strong>{{ $result['event'] }}</strong>. Gate ini: <strong>{{ $result['gate_event'] }}</strong>.</p>
                    <p class="mt-1 text-xs">Tiket tidak ditandai terpakai.</p>
                </div>
            @else
                <div class="mt-5 rounded-2xl border border-amber-400/40 bg-amber-400/10 p-6 text-amber-200" role="alert">
                    <p class="text-2xl font-bold">Tiket tidak ditemukan</p>
                    <p class="mt-2 text-sm">Periksa kembali kode tiket atau pastikan pesanan sudah dibayar.</p>
                </div>
            @endif
        @endif
    </div>
@endsection
