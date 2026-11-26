<?php

// config/concert.php
// used for business rule settings read by services

return [
    'otp_ttl_minutes' => (int) env('OTP_TTL_MINUTES', 10),
    'otp_max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 10),
    'order_ttl_minutes' => (int) env('ORDER_TTL_MINUTES', 60),
    'max_tickets_per_order' => (int) env('MAX_TICKETS_PER_ORDER', 10),
    'max_pending_orders_per_user' => (int) env('MAX_PENDING_ORDERS_PER_USER', 3),
    'payment_simulation' => (bool) env('PAYMENT_SIMULATION', env('APP_ENV') !== 'production'),
];
