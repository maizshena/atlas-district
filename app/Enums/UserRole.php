<?php

namespace App\Enums;

// app/Enums/UserRole.php peran pengguna dan helper izin dasar
enum UserRole: string
{
    case Customer = 'CUSTOMER';
    case EventOrganizer = 'EVENT_ORGANIZER';
    case Admin = 'ADMIN';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Pembeli',
            self::EventOrganizer => 'Event organizer',
            self::Admin => 'Admin',
        };
    }

    public function canManageEvents(): bool
    {
        return $this === self::Admin || $this === self::EventOrganizer;
    }
}
