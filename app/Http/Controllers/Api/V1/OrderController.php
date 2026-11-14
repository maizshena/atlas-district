<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/V1/OrderController.php
// customer orders, ownership enforced with a scoped query
class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', $request->user()->id)
            ->with('items.ticketCategory.event')
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));

        return ApiResponse::success('Daftar pesanan.', ApiResponse::paginated($orders));
    }

    public function store(StoreOrderRequest $request, OrderService $orders): JsonResponse
    {
        $order = $orders->create($request->user(), $request->validated('items'));

        return ApiResponse::success('Pesanan berhasil dibuat. Selesaikan pembayaran sebelum batas waktu.', $order, 201);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->ensureOwned($request, $order);

        return ApiResponse::success('Detail pesanan.', $order->load('items.ticketCategory.event', 'tickets'));
    }

    public function cancel(Request $request, Order $order, OrderService $orders): JsonResponse
    {
        $this->ensureOwned($request, $order);

        if (! $orders->cancel($order)) {
            return ApiResponse::error('Hanya pesanan berstatus PENDING yang dapat dibatalkan.', ['status' => $order->status], 409);
        }

        return ApiResponse::success('Pesanan dibatalkan.', $order->fresh('items.ticketCategory.event'));
    }

    public function pay(Request $request, Order $order, PaymentService $payments): JsonResponse
    {
        $this->ensureOwned($request, $order);

        return ApiResponse::success('Pembayaran berhasil.', $payments->pay($order));
    }

    private function ensureOwned(Request $request, Order $order): void
    {
        abort_if($order->user_id !== $request->user()->id, 404);
    }
}
