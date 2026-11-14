<?php

return [
    'otp_ttl_minutes' => (int) env('OTP_TTL_MINUTES', 10),
    'order_ttl_minutes' => (int) env('ORDER_TTL_MINUTES', 60),
    'max_tickets_per_order' => (int) env('MAX_TICKETS_PER_ORDER', 10),
];
