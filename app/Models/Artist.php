<?php

// app/Models/Artist.php
// used for artist entity and relations

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Artist extends Model
{
    protected $fillable = ['name', 'bio', 'photo_url'];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_artist')
            ->using(EventArtist::class)
            ->withTimestamps();
    }
}
