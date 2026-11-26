<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventNoticeRequest;
use App\Models\Event;
use App\Models\EventNotice;
use App\Services\EventNoticeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

// app/Http/Controllers/Web/Admin/EventNoticeController.php pengumuman manual di bawah banner event
class EventNoticeController extends Controller
{
    public function store(StoreEventNoticeRequest $request, Event $event, EventNoticeService $notices): RedirectResponse
    {
        $notices->post($event, $request->user(), $request->validated('message'), (int) $request->validated('days'));

        return back()->with('status', 'Pengumuman ditampilkan di halaman event.');
    }

    public function destroy(EventNotice $notice, EventNoticeService $notices): RedirectResponse
    {
        Gate::authorize('manageNotices', $notice->event);

        $notices->expire($notice);

        return back()->with('status', 'Pengumuman disembunyikan.');
    }
}
