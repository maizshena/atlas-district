<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ValidateTicketRequest;
use App\Models\Event;
use App\Models\Ticket;
use App\Services\TicketService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/V1/TicketController.php
// ticket listing for the owner and gate validation for admins
class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tickets = Ticket::query()
            ->with(['order', 'orderItem.ticketCategory.event'])
            ->whereHas('order', fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));

        return ApiResponse::success('Daftar tiket.', ApiResponse::paginated($tickets));
    }

    public function validate(ValidateTicketRequest $request, TicketService $tickets): JsonResponse
    {
        $event = Event::query()->findOrFail($request->integer('event_id'));
        $result = $tickets->validate($request->validated('ticket_code'), $event);

        return match ($result['result']) {
            'not_found' => ApiResponse::error('Tiket tidak ditemukan atau pesanan belum dibayar.', null, 404),
            'wrong_event' => ApiResponse::error('Tiket bukan milik event ini.', [
                'ticket_event' => $result['event'],
                'gate_event' => $result['gate_event'],
            ], 422),
            'used' => ApiResponse::error('Tiket sudah digunakan.', ['used_at' => $result['used_at_iso']], 400),
            default => ApiResponse::success('Tiket valid. Silakan masuk.', $result['ticket']),
        };
    }
}
