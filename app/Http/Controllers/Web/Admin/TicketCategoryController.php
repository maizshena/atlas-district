<?php

namespace App\Http\Controllers\Web\Admin;

use App\Exceptions\ConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketCategory\StoreTicketCategoryRequest;
use App\Http\Requests\TicketCategory\UpdateTicketCategoryRequest;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Services\TicketCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/TicketCategoryController.php
// ticket categories scoped to an event
class TicketCategoryController extends Controller
{
    public function index(Event $event): View
    {
        return view('admin.ticket-categories.index', [
            'event' => $event,
            'categories' => $event->ticketCategories()->orderBy('price')->get(),
        ]);
    }

    public function create(Event $event): View
    {
        return view('admin.ticket-categories.form', ['event' => $event, 'category' => new TicketCategory]);
    }

    public function store(StoreTicketCategoryRequest $request, Event $event, TicketCategoryService $categories): RedirectResponse
    {
        $categories->store([...$request->validated(), 'event_id' => $event->id]);

        return redirect()->route('admin.events.ticket-categories.index', $event)->with('status', 'Kategori tiket berhasil dibuat.');
    }

    public function edit(TicketCategory $ticket_category): View
    {
        return view('admin.ticket-categories.form', ['event' => $ticket_category->event, 'category' => $ticket_category]);
    }

    public function update(UpdateTicketCategoryRequest $request, TicketCategory $ticket_category, TicketCategoryService $categories): RedirectResponse
    {
        $categories->update($ticket_category, $request->validated());

        return redirect()->route('admin.events.show', $ticket_category->event_id)->with('status', 'Kategori tiket berhasil diperbarui.');
    }

    public function destroy(TicketCategory $ticket_category, TicketCategoryService $categories): RedirectResponse
    {
        $eventId = $ticket_category->event_id;

        try {
            $categories->delete($ticket_category);
        } catch (ConflictException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.events.show', $eventId)->with('status', 'Kategori tiket berhasil dihapus.');
    }
}
