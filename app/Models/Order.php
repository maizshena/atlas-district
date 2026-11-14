<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'order_number', 'status', 'total_amount', 'expires_at', 'paid_at'];

    protected $attributes = ['status' => 'PENDING'];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_amount' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    // app/Models/Order.php
    // pending order whose expiry has passed
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Pending)->where('expires_at', '<', now());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
