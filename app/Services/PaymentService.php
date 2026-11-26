<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Mail\TicketConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class PaymentService
{
    public function pay(Order $order): Order
    {
        if (! config('concert.payment_simulation')) {
            throw ValidationException::withMessages(['order' => ['Pembayaran online belum tersedia.']]);
        }
        $result = DB::transaction(function () use ($order): bool {
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== OrderStatus::Pending) {
                throw ValidationException::withMessages(['order' => ['Pesanan tidak berstatus menunggu pembayaran.']]);
            }

            if ($locked->expires_at->isPast()) {
                app(OrderService::class)->cancelExpired($locked);

                return false;
            }

            foreach ($locked->items()->get() as $item) {
                for ($index = 0; $index < $item->quantity; $index++) {
                    $this->createTicket($locked, $item);
                }
            }

            $locked->update(['status' => OrderStatus::Paid, 'paid_at' => now()]);

            return true;
        }, 3);

        if (! $result) {
            throw ValidationException::withMessages(['order' => ['Pesanan sudah kedaluwarsa.']]);
        }

        $paidOrder = $order->fresh(['user', 'items.ticketCategory.event', 'tickets']);
        try {
            Mail::to($paidOrder->user->email)->queue(new TicketConfirmationMail($paidOrder));
        } catch (Throwable $exception) {
            Log::error('Ticket confirmation email could not be queued after payment.', [
                'order_id' => $paidOrder->id,
                'message' => $exception->getMessage(),
            ]);
        }

        return $paidOrder;
    }

    private function createTicket(Order $order, OrderItem $item): Ticket
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return Ticket::query()->create([
                    'order_id' => $order->id,
                    'order_item_id' => $item->id,
                    'ticket_code' => $this->ticketCode(),
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                if ($attempt === 4) {
                    throw $exception;
                }
            }
        }

        throw new RuntimeException('Unable to generate a unique ticket code.');
    }

    private function ticketCode(): string
    {
        $digits = '';
        for ($index = 0; $index < 12; $index++) {
            $digits .= (string) random_int(0, 9);
        }

        return substr($digits, 0, 4).'-'.substr($digits, 4, 4).'-'.substr($digits, 8, 4);
    }
}
