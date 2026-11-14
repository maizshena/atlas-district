<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['venue_id', 'name', 'slug', 'description', 'start_date', 'end_date', 'is_closed', 'banner_url'];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_closed' => 'boolean',
        ];
    }

    // app/Models/Event.php
    // public catalog filter: not closed and not finished yet
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('is_closed', false)->where('end_date', '>=', now());
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when(filled($term), fn (Builder $builder) => $builder->where('name', 'ilike', '%'.$term.'%'));
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'event_artist')->withTimestamps();
    }

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }
}
