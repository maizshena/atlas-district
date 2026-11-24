<?php

// config/concert.php
// used for business rule settings read by services

return [
    'otp_ttl_minutes' => (int) env('OTP_TTL_MINUTES', 10),
    'order_ttl_minutes' => (int) env('ORDER_TTL_MINUTES', 60),
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:3000'),
];