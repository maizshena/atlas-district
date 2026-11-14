<?php

namespace App\Http\Controllers\Web\Admin;

use App\Exceptions\ConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Artist;
use App\Models\Event;
use App\Models\Venue;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/EventController.php
// admin crud pages for events including artist picker and banner
class EventController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::query()
            ->with('venue')
            ->withCount('ticketCategories')
            ->search($request->string('search')->toString())
            ->orderByDesc('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.events.form', [
            'event' => new Event(['start_date' => now()->addMonth()->setTime(19, 0), 'end_date' => now()->addMonth()->setTime(23, 0), 'is_closed' => false]),
            'venues' => Venue::query()->orderBy('name')->get(),
            'artists' => Artist::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEventRequest $request, EventService $events): RedirectResponse
    {
        $events->store($request->validated());

        return redirect()->route('admin.events.index')->with('status', 'Event berhasil dibuat.');
    }

    public function show(Event $event): View
    {
        return view('admin.events.show', [
            'event' => $event->load(['venue', 'artists', 'ticketCategories']),
        ]);
    }

    public function edit(Event $event): View
    {
        return view('admin.events.form', [
            'event' => $event->load('artists'),
            'venues' => Venue::query()->orderBy('name')->get(),
            'artists' => Artist::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event, EventService $events): RedirectResponse
    {
        $events->update($event, $request->validated());

        return redirect()->route('admin.events.index')->with('status', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event, EventService $events): RedirectResponse
    {
        try {
            $events->delete($event);
        } catch (ConflictException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.events.index')->with('status', 'Event berhasil dihapus.');
    }
}
