{{-- resources/views/components/gate/result-panel.blade.php memilih kartu hasil sesuai status validasi --}}
@props(['result', 'gateEventName' => null])

@switch($result['result'])
    @case('valid')
        @php($ticket = $result['ticket'])
        <x-gate.result-card tone="green" icon="check" title="Tiket valid" role="status" class="animate-pop">
            <p class="num mt-1 break-all text-heading">{{ $ticket->ticket_code }}</p>
            <x-slot:footer>
                <dl class="mt-4 grid gap-2 text-caption sm:grid-cols-3">
                    <div><dt class="opacity-70">Event</dt><dd class="font-medium">{{ $ticket->orderItem->ticketCategory->event->name }}</dd></div>
                    <div><dt class="opacity-70">Kategori</dt><dd class="font-medium">{{ $ticket->orderItem->ticketCategory->name }}</dd></div>
                    <div><dt class="opacity-70">Pesanan</dt><dd class="num font-medium">{{ $ticket->order->order_number }}</dd></div>
                </dl>
                @if ($gateEventName)
                    <p class="mt-3 text-micro opacity-70">Gate: {{ $gateEventName }}</p>
                @endif
            </x-slot:footer>
        </x-gate.result-card>
        @break

    @case('used')
        <x-gate.result-card tone="orange" icon="alert" title="Tiket sudah digunakan">
            <p class="mt-1 text-caption">Waktu pemakaian: {{ $result['used_at'] }}</p>
        </x-gate.result-card>
        @break

    @case('wrong_event')
        <x-gate.result-card tone="pink" icon="cross" title="Tiket bukan untuk event ini">
            <p class="mt-1 text-caption">Tiket milik <strong>{{ $result['event'] }}</strong>. Gate ini <strong>{{ $result['gate_event'] }}</strong>.</p>
            <p class="mt-1 text-micro">Tiket tidak ditandai terpakai.</p>
        </x-gate.result-card>
        @break

    @default
        <x-gate.result-card tone="pink" icon="cross" title="Tiket tidak ditemukan">
            <p class="mt-1 text-caption">Periksa kembali kode tiket atau pastikan pesanan sudah dibayar.</p>
        </x-gate.result-card>
@endswitch