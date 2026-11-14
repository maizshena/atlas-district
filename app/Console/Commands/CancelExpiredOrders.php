<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

// app/Console/Commands/CancelExpiredOrders.php
// releases quota of pending orders past their expiry, scheduled every minute
class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';

    protected $description = 'Cancel expired pending orders and release reserved ticket quantities';

    public function handle(OrderService $orders): int
    {
        $count = $orders->cancelExpiredOrders();
        $this->info("Cancelled {$count} expired orders.");

        return self::SUCCESS;
    }
}
