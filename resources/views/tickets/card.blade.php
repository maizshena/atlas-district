@props(['ticket'])

<x-bento.tile span="3x2" as="li" tone="blue" class="!p-3 text-white">
    <div class="flex h-full flex-col gap-3">
        <div class="rounded-btn bg-white p-2 animate-reveal">
            <img src="{{ route('tickets.qr', $ticket) }}" alt="QR tiket {{ $ticket->ticket_code }}" loading="lazy" width="280" height="280" class="mx-auto aspect-square w-full max-w-[220px] object-contain {{ $ticket->is_used ? 'opacity-40 grayscale' : '' }}">
        </div>
        <div class="min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="text-micro font-semibold uppercase tracking-wider">{{ $ticket->orderItem->ticketCategory->name }}</p>
                @if ($ticket->is_used)
                    <x-chip tone="solid-pink"><x-icon name="cross" :size="12" /> Sudah digunakan</x-chip>
                @else
                    <x-chip tone="solid-green"><x-icon name="check" :size="12" /> Belum digunakan</x-chip>
                @endif
            </div>
            <p class="num mt-1 break-all text-heading text-white">{{ $ticket->ticket_code }}</p>
            @if ($ticket->is_used)
                <p class="mt-1 text-micro text-gray">Dipakai {{ \App\Support\Formatters::jakartaDateTime($ticket->used_at) }}</p>
            @endif
        </div>
    </div>
</x-bento.tile>