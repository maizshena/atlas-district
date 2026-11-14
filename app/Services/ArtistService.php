<?php

namespace App\Services;

use App\Models\Artist;

// app/Services/ArtistService.php
// crud for artists with cloudinary photo handling
class ArtistService
{
    public function __construct(private MediaService $media) {}

    public function store(array $data): Artist
    {
        if (isset($data['photo'])) {
            $data['photo_url'] = $this->media->upload($data['photo'], 'concert-district/artists');
        }
        unset($data['photo']);

        return Artist::query()->create($data);
    }

    public function update(Artist $artist, array $data): Artist
    {
        $oldUrl = $artist->photo_url;
        if (isset($data['photo'])) {
            $data['photo_url'] = $this->media->upload($data['photo'], 'concert-district/artists');
        }
        unset($data['photo']);

        $artist->update($data);

        if ($oldUrl !== $artist->photo_url) {
            $this->media->delete($oldUrl);
        }

        return $artist->refresh();
    }

    public function delete(Artist $artist): void
    {
        $url = $artist->photo_url;
        $artist->delete();
        $this->media->delete($url);
    }
}
