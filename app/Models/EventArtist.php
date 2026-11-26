<?php

// app/Models/EventArtist.php
// used for pivot model between events and artists

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EventArtist extends Pivot
{
    protected $table = 'event_artist';

    public $incrementing = false;

    protected $fillable = ['event_id', 'artist_id'];
}
