<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = ['name', 'slug', 'bio', 'photo_url'];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_artist')->withTimestamps();
    }
}
