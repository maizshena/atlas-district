<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/V1/EventController.php
// public event catalog by slug, admin writes by id
class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $events = Event::query()
            ->with(['venue', 'artists', 'ticketCategories'])
            ->when($request->filled('search'), fn (Builder $query) => $query->where('name', 'ilike', '%'.$request->string('search')->toString().'%'))
            ->when($request->filled('venue_id'), fn (Builder $query) => $query->where('venue_id', $request->integer('venue_id')))
            ->when($request->boolean('upcoming'), fn (Builder $query) => $query->upcoming())
            ->orderBy('start_date')
            ->paginate(min($request->integer('per_page', 15), 100))
            ->published();

        return ApiResponse::success('Daftar event.', ApiResponse::paginated($events));
    }

    public function show(string $slug): JsonResponse
    {
        $event = Event::query()->with(['venue', 'artists', 'ticketCategories'])->where('slug', $slug)->firstOrFail();

        return ApiResponse::success('Detail event.', $event);
    }

    public function showById(Event $event): JsonResponse
    {
        return ApiResponse::success('Detail event.', $event->load(['venue', 'artists', 'ticketCategories']));
    }

    public function store(StoreEventRequest $request, EventService $events): JsonResponse
    {
        return ApiResponse::success('Event berhasil dibuat.', $events->store($request->validated()), 201);
    }

    public function update(UpdateEventRequest $request, Event $event, EventService $events): JsonResponse
    {
        return ApiResponse::success('Event berhasil diperbarui.', $events->update($event, $request->validated()));
    }

    public function destroy(Event $event, EventService $events): JsonResponse
    {
        $events->delete($event);

        return ApiResponse::success('Event berhasil dihapus.');
    }
}
