<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Event::class);

        $status = strtoupper($request->string('status')->toString());
        $filter = OrderStatus::tryFrom($status);

        $orders = Order::query()
            ->with(['user', 'items.ticketCategory.event'])
            ->visibleTo($request->user())
            ->when($filter !== null, fn ($query) => $query->where('status', $filter))
            ->when($request->filled('search'), fn ($query) => $query->whereRaw("lower(order_number) like ? escape '\\'", [LikeEscaper::contains($request->string('search')->toString())]))->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $orders, 'status' => $status]);
    }
}
