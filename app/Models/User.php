<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// app/Models/User.php pengguna dengan peran enum dan rute beranda per peran
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isEventOrganizer(): bool
    {
        return $this->role === UserRole::EventOrganizer;
    }

    public function canManageEvents(): bool
    {
        return $this->role?->canManageEvents() ?? false;
    }

    public function homeRouteName(): string
    {
        return match (true) {
            $this->isAdmin() => 'admin.dashboard',
            $this->isEventOrganizer() => 'admin.events.index',
            default => 'events.index',
        };
    }

    public function otp()
    {
        return $this->hasOne(Otp::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function organizedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function tickets()
    {
        return Ticket::query()->whereHas('order', fn ($query) => $query->where('user_id', $this->id));
    }
}