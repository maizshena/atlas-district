<?php

namespace App\Services;

use App\Exceptions\ConflictException;
use App\Models\Venue;

// app/Services/VenueService.php
// crud for venues with cloudinary image handling
class VenueService
{
    public function __construct(private MediaService $media) {}

    public function store(array $data): Venue
    {
        if (isset($data['image'])) {
            $data['image_url'] = $this->media->upload($data['image'], 'concert-district/venues');
        }
        unset($data['image']);

        return Venue::query()->create($data);
    }

    public function update(Venue $venue, array $data): Venue
    {
        $oldUrl = $venue->image_url;
        if (isset($data['image'])) {
            $data['image_url'] = $this->media->upload($data['image'], 'concert-district/venues');
        }
        unset($data['image']);

        $venue->update($data);

        if ($oldUrl !== $venue->image_url) {
            $this->media->delete($oldUrl);
        }

        return $venue->refresh();
    }

    public function delete(Venue $venue): void
    {
        if ($venue->events()->exists()) {
            throw new ConflictException('Venue masih dipakai oleh event aktif. Hapus atau pindahkan event tersebut lebih dulu.');
        }

        $url = $venue->image_url;
        $venue->delete();
        $this->media->delete($url);
    }
}
