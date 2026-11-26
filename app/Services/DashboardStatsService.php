<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;

// app/Services/DashboardStatsService.php angka dashboard admin dengan cache singkat
class DashboardStatsService
{
    public const CACHE_KEY = 'admin.dashboard.summary';

    private const CACHE_SECONDS = 60;

    /**
     * @return array{eventCount: int, soldCount: int, revenue: int, scannedCount: int, pendingCount: int}
     */
    public function summary(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_SECONDS, fn (): array => [
            'eventCount' => Event::query()->count(),
            'soldCount' => (int) OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', OrderStatus::Paid->value)
                ->sum('order_items.quantity'),
            'revenue' => (int) Order::query()->where('status', OrderStatus::Paid)->sum('total_amount'),
            'scannedCount' => Ticket::query()->where('is_used', true)->count(),
            'pendingCount' => Order::query()->where('status', OrderStatus::Pending)->count(),
        ]);
    }
}
