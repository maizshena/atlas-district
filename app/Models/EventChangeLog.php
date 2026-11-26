<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// app/Models/EventChangeLog.php riwayat perubahan event dengan nilai lama dan baru
class EventChangeLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['event_id', 'user_id', 'changes'];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
