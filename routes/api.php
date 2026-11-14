<?php

use App\Http\Controllers\Api\V1\ArtistController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\TicketCategoryController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\VenueController;
use Illuminate\Support\Facades\Route;

// routes/api.php
// public catalog, admin writes, token auth and customer orders

Route::get('venues', [VenueController::class, 'index']);
Route::get('venues/{venue}', [VenueController::class, 'show'])->whereNumber('venue');
Route::get('artists', [ArtistController::class, 'index']);
Route::get('artists/{artist}', [ArtistController::class, 'show'])->whereNumber('artist');
Route::get('events', [EventController::class, 'index']);
Route::get('events/{slug}', [EventController::class, 'show']);
Route::get('ticket-categories', [TicketCategoryController::class, 'index']);
Route::get('ticket-categories/{ticketCategory}', [TicketCategoryController::class, 'show'])->whereNumber('ticketCategory');

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:otp-send');
    Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:otp-verify');
    Route::post('resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:otp-send');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:sanctum', 'verified', 'admin'])->group(function (): void {
    Route::post('venues', [VenueController::class, 'store']);
    Route::put('venues/{venue}', [VenueController::class, 'update'])->whereNumber('venue');
    Route::patch('venues/{venue}', [VenueController::class, 'update'])->whereNumber('venue');
    Route::delete('venues/{venue}', [VenueController::class, 'destroy'])->whereNumber('venue');

    Route::post('artists', [ArtistController::class, 'store']);
    Route::put('artists/{artist}', [ArtistController::class, 'update'])->whereNumber('artist');
    Route::patch('artists/{artist}', [ArtistController::class, 'update'])->whereNumber('artist');
    Route::delete('artists/{artist}', [ArtistController::class, 'destroy'])->whereNumber('artist');

    Route::post('events', [EventController::class, 'store']);
    Route::get('admin/events/{event}', [EventController::class, 'showById'])->whereNumber('event');
    Route::put('events/{event}', [EventController::class, 'update'])->whereNumber('event');
    Route::patch('events/{event}', [EventController::class, 'update'])->whereNumber('event');
    Route::delete('events/{event}', [EventController::class, 'destroy'])->whereNumber('event');

    Route::post('ticket-categories', [TicketCategoryController::class, 'store']);
    Route::put('ticket-categories/{ticketCategory}', [TicketCategoryController::class, 'update'])->whereNumber('ticketCategory');
    Route::patch('ticket-categories/{ticketCategory}', [TicketCategoryController::class, 'update'])->whereNumber('ticketCategory');
    Route::delete('ticket-categories/{ticketCategory}', [TicketCategoryController::class, 'destroy'])->whereNumber('ticketCategory');

    Route::post('tickets/validate', [TicketController::class, 'validate']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function (): void {
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show'])->whereNumber('order');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->whereNumber('order');
    Route::post('orders/{order}/pay', [OrderController::class, 'pay'])->whereNumber('order');
    Route::get('tickets', [TicketController::class, 'index']);
});
