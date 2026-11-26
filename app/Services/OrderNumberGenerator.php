<?php

namespace App\Services;

// app/Services/OrderNumberGenerator.php nomor pesanan acak tanpa lock global dan tanpa batas harian
class OrderNumberGenerator
{
    public function next(): string
    {
        return 'INV-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
    }
}
