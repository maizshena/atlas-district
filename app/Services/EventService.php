<?php

namespace App\Services;

use App\Exceptions\ConflictException;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

// app/Services/EventService.php
// crud for events including artist pivot sync and banner upload
class EventService
{
    public function __construct(private MediaService $media) {}

    public function store(array $data): Event
    {
        $artistIds = $data['artist_ids'] ?? null;
        unset($data['artist_ids']);

        if (isset($data['banner'])) {
            $data['banner_url'] = $this->media->upload($data['banner'], 'concert-district/events');
        }
        unset($data['banner']);

        return DB::transaction(function () use ($data, $artistIds): Event {
            $event = Event::query()->create($data);
            if ($artistIds !== null) {
                $event->artists()->sync($artistIds);
            }

            return $event->load(self::RELATIONS);
        });
    }

    public function update(Event $event, array $data): Event
    {
        $artistIds = array_key_exists('artist_ids', $data) ? $data['artist_ids'] : null;
        unset($data['artist_ids']);

        $oldUrl = $event->banner_url;
        if (isset($data['banner'])) {
            $data['banner_url'] = $this->media->upload($data['banner'], 'concert-district/events');
        }
        unset($data['banner']);

        DB::transaction(function () use ($event, $data, $artistIds): void {
            $event->update($data);
            if ($artistIds !== null) {
                $event->artists()->sync($artistIds);
            }
        });

        if ($oldUrl !== $event->banner_url) {
            $this->media->delete($oldUrl);
        }

        return $event->refresh()->load(self::RELATIONS);
    }

    public function delete(Event $event): void
    {
        $url = $event->banner_url;

        DB::transaction(function () use ($event): void {
            $usedCategoryIds = $event->ticketCategories()->pluck('id');
            $hasReservations = DB::table('order_items')->whereIn('ticket_category_id', $usedCategoryIds)->exists();

            if ($hasReservations) {
                throw new ConflictException('Event sudah pernah dipesan sehingga tidak dapat dihapus. Tutup event sebagai gantinya.');
            }

            $event->delete();
        });

        $this->media->delete($url);
    }

    private const RELATIONS = ['venue', 'artists', 'ticketCategories'];
}
