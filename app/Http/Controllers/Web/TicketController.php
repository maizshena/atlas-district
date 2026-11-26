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

// app/Http/Controllers/Web/TicketController.php daftar tiket berpaginasi dan qr dengan etag dan cache lama
class TicketController extends Controller
{
    public function index(Request $request, TicketService $tickets): View
    {
        $userId = $request->user()->id;

        return view('tickets.index', [
            'orders' => $tickets->paidOrdersWithTickets($userId),
            'pendingOrders' => Order::query()
                ->where('user_id', $userId)
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

        $etag = '"'.md5($ticket->ticket_code).'"';
        $headers = ['ETag' => $etag, 'Cache-Control' => 'private, max-age=604800, immutable'];

        if (str_replace('W/', '', (string) $request->header('If-None-Match')) === $etag) {
            return response('', 304, $headers);
        }

        return response($qrCodes->png($ticket->ticket_code), 200, $headers + ['Content-Type' => 'image/png']);
    }
}
