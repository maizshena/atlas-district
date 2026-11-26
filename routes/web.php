<?php

use App\Http\Controllers\Web\Admin\ArtistController as AdminArtistController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\EventController as AdminEventController;
use App\Http\Controllers\Web\Admin\EventReviewController as AdminEventReviewController;
use App\Http\Controllers\Web\Admin\GateController;
use App\Http\Controllers\Web\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Web\Admin\TicketCategoryController as AdminTicketCategoryController;
use App\Http\Controllers\Web\Admin\VenueController as AdminVenueController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\EventController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\TicketController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;

Paginator::useTailwind();

Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('auth.login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:otp-send')->name('auth.register');
    Route::get('/verify-otp', [AuthController::class, 'showVerify'])->name('auth.verify');
    Route::post('/verify-otp', [AuthController::class, 'verify'])->middleware('throttle:otp-verify')->name('auth.verify.submit');
    Route::post('/resend-otp', [AuthController::class, 'resend'])->middleware('throttle:otp-send')->name('auth.resend');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->whereNumber('order')->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->whereNumber('order')->name('orders.cancel');
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->whereNumber('order')->name('orders.pay');
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}/qr', [TicketController::class, 'qr'])->whereNumber('ticket')->name('tickets.qr');
});

Route::middleware(['auth', 'verified', 'organizer'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::resource('events', AdminEventController::class)->except(['destroy']);
    Route::resource('events.ticket-categories', AdminTicketCategoryController::class)->except(['show'])->shallow();

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');

    Route::get('/gate', [GateController::class, 'index'])->name('gate');
    Route::post('/gate', [GateController::class, 'validate'])->middleware('throttle:gate-scan')->name('gate.validate');

    Route::post('events/{event}/notices', [AdminEventNoticeController::class, 'store'])->middleware('throttle:20,1')->name('events.notices.store');
    Route::delete('event-notices/{notice}', [AdminEventNoticeController::class, 'destroy'])->name('event-notices.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('venues', AdminVenueController::class)->except(['show']);
    Route::resource('artists', AdminArtistController::class)->except(['show']);

    Route::delete('events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
    Route::patch('events/{event}/publish', [AdminEventReviewController::class, 'publish'])->name('events.publish');
    Route::patch('events/{event}/reject', [AdminEventReviewController::class, 'reject'])->name('events.reject');
});
