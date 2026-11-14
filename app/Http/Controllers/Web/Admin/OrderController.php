<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

// app/Http/Controllers/Web/Admin/OrderController.php
// read only order list with status filter
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = strtoupper($request->string('status')->toString());
        $filter = OrderStatus::tryFrom($status);

        $orders = Order::query()
            ->with(['user', 'items.ticketCategory.event'])
            ->when($filter !== null, fn ($query) => $query->where('status', $filter))
            ->when($request->filled('search'), fn ($query) => $query->where('order_number', 'ilike', '%'.$request->string('search')->toString().'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $orders, 'status' => $status]);
    }
}
