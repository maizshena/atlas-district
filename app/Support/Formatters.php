<?php

namespace App\Support;

use Illuminate\Support\Carbon;

// app/Support/Formatters.php
// shared rupiah and jakarta time formatting for views and mail
class Formatters
{
    public static function rupiah(int|float|string|null $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }

    public static function jakartaDate(?Carbon $value): string
    {
        return $value === null ? '-' : $value->timezone('Asia/Jakarta')->translatedFormat('l, d F Y');
    }

    public static function jakartaDateTime(?Carbon $value, string $suffix = ' WIB'): string
    {
        return $value === null ? '-' : $value->timezone('Asia/Jakarta')->format('d/m/Y H:i').$suffix;
    }
}
