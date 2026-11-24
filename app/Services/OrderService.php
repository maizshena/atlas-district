<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\ConflictException;
use App\Models\Order;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

// app/Services/OrderService.php
// used for order creation, quota reservation, cancellation and expiry cleanup
class OrderService
{
    private const MAX_ORDER_NUMBER_ATTEMPTS = 5;

    public function create(User $user, array $items): Order
    {
        $items = $this->normalizeItems($items);

        if (array_sum(array_column($items, 'quantity')) > config('concert.max_tickets_per_order')) {
            throw ValidationException::withMessages(['items' => ['Maksimal '.config('concert.max_tickets_per_order').' tiket per pesanan.']]);
        }

        for ($attempt = 1; $attempt <= self::MAX_ORDER_NUMBER_ATTEMPTS; $attempt++) {
            try {
                return DB::transaction(fn (): Order => $this->createWithinTransaction($user, $items), 3);
            } catch (UniqueConstraintViolationException $exception) {
                if ($attempt === self::MAX_ORDER_NUMBER_ATTEMPTS) {
                    throw $exception;
                }
            }
        }

        throw new RuntimeException('Unable to create order number.');
    }

    public function cancel(Order $order): bool
    {
        return DB::transaction(function () use ($order): bool {
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== OrderStatus::Pending) {
                return false;
            }

            $this->releaseReservedQuantities($locked);
            $locked->update(['status' => OrderStatus::Cancelled]);

            return true;
        }, 3);
    }

    public function cancelExpired(Order $order): void
    {
        $this->releaseReservedQuantities($order);
        $order->update(['status' => OrderStatus::Expired]);
    }

    public function cancelExpiredOrders(): int
    {
        $ids = Order::expired()->orderBy('id')->limit(200)->pluck('id');
        $cancelled = 0;

        foreach ($ids as $id) {
            $didCancel = DB::transaction(function () use ($id): bool {
                $order = Order::query()->whereKey($id)->lockForUpdate()->first();
                if ($order === null || $order->status !== OrderStatus::Pending || $order->expires_at->isFuture()) {
                    return false;
                }

                $this->cancelExpired($order);

                return true;
            }, 3);
            $cancelled += (int) $didCancel;
        }

        return $cancelled;
    }

    private function createWithinTransaction(User $user, array $items): Order
    {
        $ids = array_map(fn (array $item): int => $item['ticket_category_id'], $items);
        $categories = TicketCategory::query()->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');

        if ($categories->count() !== count($ids)) {
            throw ValidationException::withMessages(['items' => ['Kategori tiket tidak ditemukan.']]);
        }

        $event = $categories->first()->event;
        if ($categories->pluck('event_id')->unique()->count() !== 1) {
            throw ValidationException::withMessages(['items' => ['Satu pesanan hanya boleh berisi kategori dari satu event.']]);
        }
        if ($event === null || $event->status !== EventStatus::Published || $event->is_closed || $event->end_date->isPast()) {
            throw ValidationException::withMessages(['items' => ['Event belum tayang, sudah ditutup, atau telah berakhir.']]);
        }

        $total = 0;
        foreach ($items as $item) {
            $category = $categories->get($item['ticket_category_id']);
            if ($category->quota - $category->sold < $item['quantity']) {
                throw ValidationException::withMessages(['items' => ['Kuota tiket '.$category->name.' tidak mencukupi.']]);
            }
            $total += $category->price * $item['quantity'];
        }

        $order = Order::query()->create([
            'user_id' => $user->id,
            'order_number' => $this->nextOrderNumber(),
            'status' => OrderStatus::Pending,
            'total_amount' => $total,
            'expires_at' => now()->addMinutes(config('concert.order_ttl_minutes')),
        ]);

        foreach ($items as $item) {
            $category = $categories->get($item['ticket_category_id']);
            $category->increment('sold', $item['quantity']);
            $order->items()->create([
                'ticket_category_id' => $category->id,
                'quantity' => $item['quantity'],
                'unit_price' => $category->price,
            ]);
        }

        return $order->load('items.ticketCategory.event');
    }

    /**
     * @param  array<int, array{ticket_category_id: int|string, quantity: int|string}>  $items
     * @return array<int, array{ticket_category_id: int, quantity: int}>
     */
    private function normalizeItems(array $items): array
    {
        $merged = [];
        foreach ($items as $item) {
            $id = (int) $item['ticket_category_id'];
            $merged[$id] = ($merged[$id] ?? 0) + (int) $item['quantity'];
        }
        ksort($merged);

        return collect($merged)->map(fn (int $quantity, int $id): array => [
            'ticket_category_id' => $id,
            'quantity' => $quantity,
        ])->values()->all();
    }

    private function releaseReservedQuantities(Order $order): void
    {
        $items = $order->items()->orderBy('ticket_category_id')->get();
        $categories = TicketCategory::query()
            ->whereIn('id', $items->pluck('ticket_category_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $categories->get($item->ticket_category_id)?->decrement('sold', $item->quantity);
        }
    }

    private function nextOrderNumber(): string
    {
        DB::statement('SELECT pg_advisory_xact_lock(hashtext(?))', ['concert-district-order-'.now()->format('Ymd')]);

        $prefix = 'INV-'.now()->format('Ymd').'-';
        $latest = Order::query()
            ->where('order_number', 'like', $prefix.'%')
            ->orderByDesc('order_number')
            ->value('order_number');
        $sequence = $latest === null ? 1 : ((int) substr($latest, -3)) + 1;

        if ($sequence > 999) {
            throw new ConflictException('Nomor pesanan harian telah mencapai batas.');
        }

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
