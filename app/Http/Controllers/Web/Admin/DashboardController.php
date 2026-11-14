<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/DashboardController.php
// summary cards for the admin dashboard
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'eventCount' => Event::query()->count(),
            'soldCount' => (int) TicketCategory::query()->sum('sold'),
            'revenue' => Order::query()->where('status', OrderStatus::Paid)->sum('total_amount'),
            'scannedCount' => Ticket::query()->where('is_used', true)->count(),
            'pendingCount' => Order::query()->where('status', OrderStatus::Pending)->count(),
            'recentOrders' => Order::query()->with('user')->latest()->limit(8)->get(),
        ]);
    }
}
