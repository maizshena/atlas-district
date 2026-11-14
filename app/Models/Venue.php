<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Venue.php
// stadium or hall that hosts events
class Venue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'address', 'city', 'capacity', 'image_url'];

    protected function casts(): array
    {
        return ['capacity' => 'integer'];
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when(filled($term), fn (Builder $builder) => $builder->where('name', 'ilike', '%'.$term.'%'));
    }
}
