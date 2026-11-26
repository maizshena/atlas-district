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
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/EventController.php halaman event untuk admin dan organizer dengan scope kepemilikan
class EventController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Event::class);

        $events = Event::query()
            ->with(['venue', 'organizer'])
            ->withCount('ticketCategories')
            ->visibleTo($request->user())
            ->search($request->string('search')->toString())
            ->orderByDesc('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        Gate::authorize('create', Event::class);

        return view('admin.events.form', [
            'event' => new Event(['start_date' => now()->addMonth()->setTime(19, 0), 'end_date' => now()->addMonth()->setTime(23, 0), 'is_closed' => false]),
            'venues' => Venue::query()->orderBy('name')->get(),
            'artists' => Artist::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEventRequest $request, EventService $events): RedirectResponse
    {
        $events->store($request->validated(), $request->user());

        $message = $request->user()->isAdmin() ? 'Event berhasil dibuat.' : 'Event diajukan dan menunggu review admin.';

        return redirect()->route('admin.events.index')->with('status', $message);
    }

    public function show(Event $event): View
    {
        Gate::authorize('view', $event);

        return view('admin.events.show', [
            'event' => $event->load(['venue', 'artists', 'ticketCategories']),
        ]);
    }

    public function edit(Event $event): View
    {
        Gate::authorize('updateInfo', $event);

        return view('admin.events.form', [
            'event' => $event->load(['venue', 'artists']),
            'venues' => Venue::query()->orderBy('name')->get(),
            'artists' => Artist::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event, EventService $events): RedirectResponse
    {
        $events->update($event, $request->validated(), $request->user());

        return redirect()->route('admin.events.index')->with('status', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event, EventService $events): RedirectResponse
    {
        Gate::authorize('delete', $event);

        try {
            $events->delete($event);
        } catch (ConflictException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.events.index')->with('status', 'Event berhasil dihapus.');
    }
}
