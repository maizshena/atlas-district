<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Artist\StoreArtistRequest;
use App\Http\Requests\Artist\UpdateArtistRequest;
use App\Models\Artist;
use App\Services\ArtistService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/V1/ArtistController.php
// artist catalog and admin writes, thin controller over ArtistService
class ArtistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $artists = Artist::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'ilike', '%'.$request->string('search')->toString().'%'))
            ->orderBy('name')
            ->paginate(min($request->integer('per_page', 15), 100));

        return ApiResponse::success('Daftar artis.', ApiResponse::paginated($artists));
    }

    public function show(Artist $artist): JsonResponse
    {
        return ApiResponse::success('Detail artis.', $artist->load('events'));
    }

    public function store(StoreArtistRequest $request, ArtistService $artists): JsonResponse
    {
        return ApiResponse::success('Artis berhasil dibuat.', $artists->store($request->validated()), 201);
    }

    public function update(UpdateArtistRequest $request, Artist $artist, ArtistService $artists): JsonResponse
    {
        return ApiResponse::success('Artis berhasil diperbarui.', $artists->update($artist, $request->validated()));
    }

    public function destroy(Artist $artist, ArtistService $artists): JsonResponse
    {
        $artists->delete($artist);

        return ApiResponse::success('Artis berhasil dihapus.');
    }
}
