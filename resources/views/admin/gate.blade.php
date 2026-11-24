@extends('layouts.admin', ['title' => 'Validasi gate'])

@section('content')
    <div class="mx-auto max-w-4xl">
        <x-bento.page-header eyebrow="Pintu masuk" title="Validasi tiket" description="Pilih event, klik kolom kode tiket, lalu pindai barcode atau ketik kode manual." />

        <x-bento.grid>
            <x-bento.tile span="2x2" tone="raised">
                <form method="POST" action="{{ route('admin.gate.validate') }}">
                    @csrf

                    <x-select name="event_id" label="Event" required>
                        <option value="">Pilih event yang dijaga</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" @selected((int) old('event_id', $selectedEventId) === $event->id)>
                                {{ $event->name }} ({{ $event->start_date->format('d M Y') }})
                            </option>
                        @endforeach
                    </x-select>

                    <x-input label="Kode tiket" name="ticket_code" :value="old('ticket_code')" autocomplete="off" inputmode="numeric" placeholder="0000-0000-0000" maxlength="14" class="num mt-4 text-center text-title tracking-[0.3em]" required />

                    <x-button type="submit" class="mt-4 w-full" data-submit>Validasi tiket</x-button>
                </form>
            </x-bento.tile>

            <x-bento.tile span="2x2" class="flex flex-col justify-center">
                <span class="grid h-12 w-12 place-items-center rounded-full bg-surface-3 text-white"><x-icon name="scan" :size="22" /></span>
                <p class="mt-3 text-heading font-semibold text-white">Cara pakai</p>
                <ol class="mt-2 space-y-1.5 text-caption text-gray">
                    <li>1. Pilih event yang sedang dijaga.</li>
                    <li>2. Pindai QR atau ketik kode tiket.</li>
                    <li>3. Cocokkan warna hasil sebelum meloloskan penonton.</li>
                </ol>
            </x-bento.tile>

            @if (session('gate_result'))
                @php($result = session('gate_result'))
                @if ($result['result'] === 'valid')
                    @php($ticket = $result['ticket'])
                    <x-bento.tile span="4x2" tone="green" as="section" role="status" class="animate-pop">
                        <div class="flex items-start gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-white/70 text-black"><x-icon name="check" :size="22" /></span>
                            <div class="min-w-0">
                                <h2 class="text-title">Tiket valid</h2>
                                <p class="num mt-1 break-all text-heading">{{ $ticket->ticket_code }}</p>
                            </div>
                        </div>
                        <dl class="mt-4 grid gap-2 text-caption sm:grid-cols-3">
                            <div><dt class="opacity-70">Event</dt><dd class="font-medium">{{ $ticket->orderItem->ticketCategory->event->name }}</dd></div>
                            <div><dt class="opacity-70">Kategori</dt><dd class="font-medium">{{ $ticket->orderItem->ticketCategory->name }}</dd></div>
                            <div><dt class="opacity-70">Pesanan</dt><dd class="num font-medium">{{ $ticket->order->order_number }}</dd></div>
                        </dl>
                        @if (session('gate_event_name'))
                            <p class="mt-3 text-micro opacity-70">Gate: {{ session('gate_event_name') }}</p>
                        @endif
                    </x-bento.tile>
                @elseif ($result['result'] === 'used')
                    <x-bento.tile span="4x2" tone="orange" as="section" role="alert">
                        <div class="flex items-start gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-white/70 text-black"><x-icon name="alert" :size="22" /></span>
                            <div>
                                <h2 class="text-title">Tiket sudah digunakan</h2>
                                <p class="mt-1 text-caption">Waktu pemakaian: {{ $result['used_at'] }}</p>
                            </div>
                        </div>
                    </x-bento.tile>
                @elseif ($result['result'] === 'wrong_event')
                    <x-bento.tile span="4x2" tone="pink" as="section" role="alert">
                        <div class="flex items-start gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-white/70 text-black"><x-icon name="cross" :size="22" /></span>
                            <div>
                                <h2 class="text-title">Tiket bukan untuk event ini</h2>
                                <p class="mt-1 text-caption">Tiket milik <strong>{{ $result['event'] }}</strong>. Gate ini <strong>{{ $result['gate_event'] }}</strong>.</p>
                                <p class="mt-1 text-micro">Tiket tidak ditandai terpakai.</p>
                            </div>
                        </div>
                    </x-bento.tile>
                @else
                    <x-bento.tile span="4x2" tone="pink" as="section" role="alert">
                        <div class="flex items-start gap-3">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-white/70 text-black"><x-icon name="cross" :size="22" /></span>
                            <div>
                                <h2 class="text-title">Tiket tidak ditemukan</h2>
                                <p class="mt-1 text-caption">Periksa kembali kode tiket atau pastikan pesanan sudah dibayar.</p>
                            </div>
                        </div>
                    </x-bento.tile>
                @endif
            @endif
        </x-bento.grid>
    </div>
@endsection
