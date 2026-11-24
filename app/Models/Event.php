<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// app/Models/Event.php status tayang kepemilikan organizer dan scope katalog publik
class Event extends Model
{
    use HasFactory;

    protected $fillable = ['venue_id', 'name', 'slug', 'description', 'start_date', 'end_date', 'is_closed', 'banner_url'];

    protected $attributes = ['status' => EventStatus::Published->value];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_closed' => 'boolean',
            'status' => EventStatus::class,
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EventStatus::Published);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->published()->where('is_closed', false)->where('end_date', '>=', now());
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isAdmin() ? $query : $query->where('organizer_id', $user->id);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when(filled($term), fn (Builder $builder) => $builder->where('name', 'ilike', '%'.$term.'%'));
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'event_artist')->withTimestamps();
    }

    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class);
    }
}