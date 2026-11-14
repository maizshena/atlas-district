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

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-400/15 text-amber-300 ring-amber-400/30',
            self::Paid => 'bg-emerald-400/15 text-emerald-300 ring-emerald-400/30',
            self::Cancelled => 'bg-slate-500/15 text-slate-300 ring-slate-400/30',
            self::Expired => 'bg-rose-400/15 text-rose-300 ring-rose-400/30',
        };
    }
}
