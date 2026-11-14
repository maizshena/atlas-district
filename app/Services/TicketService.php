<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Ticket;
use App\Support\Formatters;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Validate a ticket at the gate of a specific event and mark it used.
     *
     * @return array{result: string, used_at?: mixed, ticket?: Ticket, event?: string}
     */
    public function validate(string $code, Event $gateEvent): array
    {
        $ticket = Ticket::query()
            ->with(['order', 'orderItem.ticketCategory.event'])
            ->where('ticket_code', $code)
            ->first();

        if ($ticket === null || $ticket->order->status !== OrderStatus::Paid) {
            return ['result' => 'not_found'];
        }

        $ticketEvent = $ticket->orderItem?->ticketCategory?->event;
        if ($ticketEvent === null || $ticketEvent->id !== $gateEvent->id) {
            return [
                'result' => 'wrong_event',
                'event' => $ticketEvent?->name ?? 'Event tidak diketahui',
                'gate_event' => $gateEvent->name,
            ];
        }

        if ($ticket->is_used) {
            return $this->usedResult($ticket);
        }

        $affected = DB::table('tickets')
            ->where('id', $ticket->id)
            ->where('is_used', false)
            ->whereExists(fn ($query) => $query->selectRaw('1')->from('orders')->whereColumn('orders.id', 'tickets.order_id')->where('orders.status', 'PAID'))
            ->update(['is_used' => true, 'used_at' => now(), 'updated_at' => now()]);

        if ($affected === 0) {
            $ticket->refresh();

            return $this->usedResult($ticket);
        }

        return ['result' => 'valid', 'ticket' => $ticket->fresh(['order', 'orderItem.ticketCategory.event'])];
    }

    /**
     * Flash data is serialized to the session, so used_at is pre-formatted as a string.
     * The API keeps the raw ISO timestamp under used_at_iso.
     *
     * @return array{result: string, used_at: string|null, used_at_iso: string|null}
     */
    private function usedResult(Ticket $ticket): array
    {
        return [
            'result' => 'used',
            'used_at' => Formatters::jakartaDateTime($ticket->used_at),
            'used_at_iso' => $ticket->used_at?->toIso8601String(),
        ];
    }

    public function ownedByUser(int $userId)
    {
        return Ticket::query()
            ->with(['order', 'orderItem.ticketCategory.event.venue'])
            ->whereHas('order', fn ($query) => $query->where('user_id', $userId))
            ->latest()
            ->get();
    }
}
