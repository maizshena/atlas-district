<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Venue\StoreVenueRequest;
use App\Http\Requests\Venue\UpdateVenueRequest;
use App\Models\Venue;
use App\Services\VenueService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/V1/VenueController.php
// venue catalog and admin writes, thin controller over VenueService
class VenueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $venues = Venue::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'ilike', '%'.$request->string('search')->toString().'%'))
            ->orderBy('name')
            ->paginate(min($request->integer('per_page', 15), 100));

        return ApiResponse::success('Daftar venue.', ApiResponse::paginated($venues));
    }

    public function show(Venue $venue): JsonResponse
    {
        return ApiResponse::success('Detail venue.', $venue->load('events'));
    }

    public function store(StoreVenueRequest $request, VenueService $venues): JsonResponse
    {
        return ApiResponse::success('Venue berhasil dibuat.', $venues->store($request->validated()), 201);
    }

    public function update(UpdateVenueRequest $request, Venue $venue, VenueService $venues): JsonResponse
    {
        return ApiResponse::success('Venue berhasil diperbarui.', $venues->update($venue, $request->validated()));
    }

    public function destroy(Venue $venue, VenueService $venues): JsonResponse
    {
        $venues->delete($venue);

        return ApiResponse::success('Venue berhasil dihapus.');
    }
}
