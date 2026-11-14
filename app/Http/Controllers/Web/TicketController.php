<?php

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\QrCodeService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

// app/Http/Controllers/Web/TicketController.php
// renders owned tickets grouped by order, qr images served per ticket
class TicketController extends Controller
{
    public function index(Request $request, TicketService $tickets): View
    {
        $items = $tickets->ownedByUser($request->user()->id)
            ->groupBy(fn ($ticket) => $ticket->order_id)
            ->map(fn ($group) => [
                'order' => $group->first()->order,
                'tickets' => $group->values(),
            ])
            ->values();

        return view('tickets.index', [
            'groups' => $items,
            'pendingOrders' => Order::query()
                ->where('user_id', $request->user()->id)
                ->where('status', OrderStatus::Pending)
                ->where('expires_at', '>', now())
                ->with('items.ticketCategory.event')
                ->latest()
                ->get(),
        ]);
    }

    public function qr(Request $request, Ticket $ticket, QrCodeService $qrCodes): Response
    {
        abort_if($ticket->order->user_id !== $request->user()->id, 404);

        return response($qrCodes->png($ticket->ticket_code), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
