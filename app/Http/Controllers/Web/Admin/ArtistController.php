<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Artist\StoreArtistRequest;
use App\Http\Requests\Artist\UpdateArtistRequest;
use App\Models\Artist;
use App\Services\ArtistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/ArtistController.php
// admin crud pages for artists
class ArtistController extends Controller
{
    public function index(Request $request): View
    {
        $artists = Artist::query()
            ->withCount('events')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'ilike', '%'.$request->string('search')->toString().'%'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.artists.index', compact('artists'));
    }

    public function create(): View
    {
        return view('admin.artists.form', ['artist' => new Artist]);
    }

    public function store(StoreArtistRequest $request, ArtistService $artists): RedirectResponse
    {
        $artists->store($request->validated());

        return redirect()->route('admin.artists.index')->with('status', 'Artis berhasil dibuat.');
    }

    public function edit(Artist $artist): View
    {
        return view('admin.artists.form', compact('artist'));
    }

    public function update(UpdateArtistRequest $request, Artist $artist, ArtistService $artists): RedirectResponse
    {
        $artists->update($artist, $request->validated());

        return redirect()->route('admin.artists.index')->with('status', 'Artis berhasil diperbarui.');
    }

    public function destroy(Artist $artist, ArtistService $artists): RedirectResponse
    {
        $artists->delete($artist);

        return redirect()->route('admin.artists.index')->with('status', 'Artis berhasil dihapus.');
    }
}
