<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// app/Http/Controllers/Web/OrderController.php
// customer order pages, same services as the api
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with('items.ticketCategory.event')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        $this->ensureOwned($request, $order);

        return view('orders.show', ['order' => $order->load('items.ticketCategory.event', 'tickets')]);
    }

    public function store(StoreOrderRequest $request, OrderService $orders): RedirectResponse
    {
        $order = $orders->create($request->user(), $request->validated('items'));

        return redirect()->route('orders.show', $order)
            ->with('status', 'Pesanan dibuat. Selesaikan pembayaran sebelum batas waktu.');
    }

    public function cancel(Request $request, Order $order, OrderService $orders): RedirectResponse
    {
        $this->ensureOwned($request, $order);

        if (! $orders->cancel($order)) {
            return back()->withErrors(['order' => 'Pesanan ini tidak dapat dibatalkan.']);
        }

        return redirect()->route('orders.show', $order)->with('status', 'Pesanan dibatalkan dan kuota tiket dikembalikan.');
    }

    public function pay(Request $request, Order $order, PaymentService $payments): RedirectResponse
    {
        $this->ensureOwned($request, $order);
        $payments->pay($order);

        return redirect()->route('orders.show', $order)->with('status', 'Pembayaran berhasil. Detail tiket dikirim ke email Anda.');
    }

    private function ensureOwned(Request $request, Order $order): void
    {
        abort_if($order->user_id !== $request->user()->id, 404);
    }
}
