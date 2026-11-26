<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

// app/Http/Controllers/Web/EventController.php
// public homepage list and event detail page
class EventController extends Controller
{
    public function index(Request $request): View
    {
        $events = Event::query()
            ->with(['venue', 'ticketCategories'])
            ->upcoming()
            ->search($request->string('search')->toString())
            ->orderBy('start_date')
            ->paginate(9)
            ->withQueryString();

        return view('events.index', ['events' => $events, 'search' => $request->string('search')->toString()]);
    }

    public function show(Request $request, string $slug): View
    {
        $event = Event::query()
            ->with(['venue', 'artists', 'ticketCategories'])
            ->where('slug', $slug)
            ->firstOrFail();

        abort_unless($event->status === EventStatus::Published || $request->user()?->can('preview', $event), 404);

        return view('events.show', ['event' => $event, 'notice' => $event->activeNotice()]);
    }
}
