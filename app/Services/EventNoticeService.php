<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventNotice;
use App\Models\User;

// app/Services/EventNoticeService.php pengumuman manual dan otomatis di bawah banner event
class EventNoticeService
{
    private const AUTOMATIC_DAYS = 7;

    public function post(Event $event, User $author, string $message, int $days): EventNotice
    {
        return EventNotice::query()->create([
            'event_id' => $event->id,
            'author_id' => $author->id,
            'message' => $message,
            'is_automatic' => false,
            'expires_at' => now()->addDays($days),
        ]);
    }

    public function announceAutomatic(Event $event, User $author, string $message): EventNotice
    {
        return EventNotice::query()->updateOrCreate(
            ['event_id' => $event->id, 'is_automatic' => true],
            ['author_id' => $author->id, 'message' => $message, 'expires_at' => now()->addDays(self::AUTOMATIC_DAYS)],
        );
    }

    public function expire(EventNotice $notice): void
    {
        $notice->update(['expires_at' => now()]);
    }
}
