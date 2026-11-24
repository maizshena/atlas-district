<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'PENDING';
    case Paid = 'PAID';
    case Cancelled = 'CANCELLED';
    case Expired = 'EXPIRED';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu bayar',
            self::Paid => 'Lunas',
            self::Cancelled => 'Dibatalkan',
            self::Expired => 'Kedaluwarsa',
        };
    }

    // app/Enums/OrderStatus.php nama tone tema dan ikon per status untuk badge
    public function tone(): string
    {
        return match ($this) {
            self::Pending => 'orange',
            self::Paid => 'green',
            self::Cancelled => 'solid-gray',
            self::Expired => 'pink',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Paid => 'check',
            self::Cancelled => 'cross',
            self::Expired => 'clock',
        };
    }
}
