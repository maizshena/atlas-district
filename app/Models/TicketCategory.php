<?php

// app/Models/TicketCategory.php
// used for ticket tier entity with remaining quota accessor

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    protected $fillable = ['event_id', 'category_name', 'price', 'quota', 'sold'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quota' => 'integer',
            'sold' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    protected function remaining(): Attribute
    {
        return Attribute::get(fn () => max(0, $this->quota - $this->sold));
    }
}