<?php

namespace App\Http\Controllers\Web\Admin;

use App\Exceptions\ConflictException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Venue\StoreVenueRequest;
use App\Http\Requests\Venue\UpdateVenueRequest;
use App\Models\Venue;
use App\Services\VenueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/VenueController.php
// admin crud pages for venues
class VenueController extends Controller
{
    public function index(Request $request): View
    {
        $venues = Venue::query()
            ->withCount('events')
            ->search($request->string('search')->toString())
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.venues.index', compact('venues'));
    }

    public function create(): View
    {
        return view('admin.venues.form', ['venue' => new Venue]);
    }

    public function store(StoreVenueRequest $request, VenueService $venues): RedirectResponse
    {
        $venues->store($request->validated());

        return redirect()->route('admin.venues.index')->with('status', 'Venue berhasil dibuat.');
    }

    public function edit(Venue $venue): View
    {
        return view('admin.venues.form', compact('venue'));
    }

    public function update(UpdateVenueRequest $request, Venue $venue, VenueService $venues): RedirectResponse
    {
        $venues->update($venue, $request->validated());

        return redirect()->route('admin.venues.index')->with('status', 'Venue berhasil diperbarui.');
    }

    public function destroy(Venue $venue, VenueService $venues): RedirectResponse
    {
        try {
            $venues->delete($venue);
        } catch (ConflictException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.venues.index')->with('status', 'Venue berhasil dihapus.');
    }
}
