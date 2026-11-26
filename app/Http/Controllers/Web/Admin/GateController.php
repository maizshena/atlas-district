<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\ValidateTicketRequest;
use App\Models\Event;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/GateController.php gate tiket dengan event terbatas pada milik organizer
class GateController extends Controller
{
    private const SESSION_KEY = 'admin.gate.event_id';

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Event::class);

        $events = Event::query()
            ->visibleTo($request->user())
            ->orderByDesc('start_date')
            ->get(['id', 'name', 'start_date', 'end_date']);

        $requestedId = $request->integer('event_id') ?: $request->session()->get(self::SESSION_KEY);

        return view('admin.gate', [
            'events' => $events,
            'selectedEventId' => $events->contains('id', $requestedId) ? $requestedId : null,
        ]);
    }

    public function validate(ValidateTicketRequest $request, TicketService $tickets): RedirectResponse
    {
        $event = Event::query()->visibleTo($request->user())->findOrFail($request->integer('event_id'));
        $request->session()->put(self::SESSION_KEY, $event->id);

        $result = $tickets->validate($request->validated('ticket_code'), $event);

        if ($result['result'] === 'wrong_event' && ! $request->user()->isAdmin()) {
            $result['event'] = 'event lain';
        }

        return redirect()
            ->route('admin.gate', ['event_id' => $event->id])
            ->with('gate_result', $result)
            ->with('gate_event_name', $event->name);
    }
}
