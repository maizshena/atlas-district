<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketCategory\StoreTicketCategoryRequest;
use App\Http\Requests\TicketCategory\UpdateTicketCategoryRequest;
use App\Models\TicketCategory;
use App\Services\TicketCategoryService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/V1/TicketCategoryController.php
// ticket category catalog and admin writes
class TicketCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = TicketCategory::query()
            ->with('event')
            ->when($request->filled('event_id'), fn ($query) => $query->where('event_id', $request->integer('event_id')))
            ->orderBy('event_id')
            ->orderBy('price')
            ->paginate(min($request->integer('per_page', 15), 100));

        return ApiResponse::success('Daftar kategori tiket.', ApiResponse::paginated($categories));
    }

    public function show(TicketCategory $ticketCategory): JsonResponse
    {
        return ApiResponse::success('Detail kategori tiket.', $ticketCategory->load('event'));
    }

    public function store(StoreTicketCategoryRequest $request, TicketCategoryService $categories): JsonResponse
    {
        return ApiResponse::success('Kategori tiket berhasil dibuat.', $categories->store($request->validated()), 201);
    }

    public function update(UpdateTicketCategoryRequest $request, TicketCategory $ticketCategory, TicketCategoryService $categories): JsonResponse
    {
        return ApiResponse::success('Kategori tiket berhasil diperbarui.', $categories->update($ticketCategory, $request->validated()));
    }

    public function destroy(TicketCategory $ticketCategory, TicketCategoryService $categories): JsonResponse
    {
        $categories->delete($ticketCategory);

        return ApiResponse::success('Kategori tiket berhasil dihapus.');
    }
}
