<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'name', 'price', 'quota', 'sold'];

    protected function casts(): array
    {
        return ['price' => 'integer', 'quota' => 'integer', 'sold' => 'integer'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
