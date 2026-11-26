<?php

namespace App\Enums;

// app/Enums/EventStatus.php status tayang event untuk alur review admin
enum EventStatus: string
{
    case PendingReview = 'PENDING_REVIEW';
    case Published = 'PUBLISHED';
    case Rejected = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::PendingReview => 'Menunggu review',
            self::Published => 'Tayang',
            self::Rejected => 'Ditolak',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::PendingReview => 'orange',
            self::Published => 'green',
            self::Rejected => 'pink',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PendingReview => 'clock',
            self::Published => 'check',
            self::Rejected => 'cross',
        };
    }
}
