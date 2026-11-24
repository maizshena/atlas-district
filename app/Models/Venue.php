<?php

// app/Models/Venue.php
// used for venue entity and relations

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = ['name', 'location', 'capacity', 'rental_price_per_day', 'image_url'];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'rental_price_per_day' => 'decimal:2',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}