<?php

use App\Http\Controllers\Admin\BookingsController;
use App\Http\Controllers\Admin\BookingSlotsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\UserBookingsHistoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect(route('dashboard')));

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    // users
    Route::get('/users/list/{role}', [UsersController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create/{role}', [UsersController::class, 'create'])->name('admin.users.create');
    Route::post('/users/store', [UsersController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/{role}', [UsersController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{user}/{role}/history', UserBookingsHistoryController::class)->name('admin.users.history');

    // bookings
    Route::get('/bookings/create', [BookingsController::class, 'create'])->name('admin.bookings.create');
    Route::post('/bookings/store', [BookingsController::class, 'store'])->name('admin.bookings.store');
    Route::get('/bookings/{booking}', [BookingsController::class, 'show'])->name('admin.bookings.show');

    // bookings slots
    Route::get('/bookings-slots/{bookingSlot}/show', [BookingSlotsController::class, 'show'])->name('admin.bookings-slots.show');
    Route::get('/bookings-slots/{bookingSlot}/edit', [BookingSlotsController::class, 'edit'])->name('admin.bookings-slots.edit');
    Route::put('/bookings-slots/{bookingSlot}/update', [BookingSlotsController::class, 'update'])->name('admin.bookings-slots.update');
});
