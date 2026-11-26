<?php

namespace App\Http\Controllers\Web\Admin;

use App\Exceptions\ConflictException;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

// app/Http/Controllers/Web/Admin/EventReviewController.php admin menayangkan atau menolak event ajuan organizer
class EventReviewController extends Controller
{
    public function publish(Event $event, EventService $events): RedirectResponse
    {
        Gate::authorize('review', $event);

        try {
            $events->publish($event);
        } catch (ConflictException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('status', 'Event dipublikasikan.');
    }

    public function reject(Event $event, EventService $events): RedirectResponse
    {
        Gate::authorize('review', $event);

        $events->reject($event);

        return back()->with('status', 'Event ditolak.');
    }
}
