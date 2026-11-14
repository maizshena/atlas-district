<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ValidateTicketRequest;
use App\Models\Event;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/GateController.php
// barcode style ticket gate, result shown via flash
class GateController extends Controller
{
    private const SESSION_KEY = 'admin.gate.event_id';

    public function index(Request $request): View
    {
        $eventId = $request->integer('event_id') ?: $request->session()->get(self::SESSION_KEY);

        return view('admin.gate', [
            'events' => Event::query()->orderByDesc('start_date')->get(['id', 'name', 'start_date', 'end_date']),
            'selectedEventId' => $eventId,
        ]);
    }

    public function validate(ValidateTicketRequest $request, TicketService $tickets): RedirectResponse
    {
        $eventId = $request->integer('event_id');
        $request->session()->put(self::SESSION_KEY, $eventId);

        $event = Event::query()->findOrFail($eventId);

        return redirect()
            ->route('admin.gate', ['event_id' => $event->id])
            ->with('gate_result', $tickets->validate($request->validated('ticket_code'), $event))
            ->with('gate_event_name', $event->name);
    }
}
