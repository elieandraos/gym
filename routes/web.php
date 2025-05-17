<?php

use App\Http\Controllers\Admin\BookingsController;
use App\Http\Controllers\Admin\BookingSlotsController;
use App\Http\Controllers\Admin\MembersController;
use App\Http\Controllers\Admin\TrainersController;
use App\Http\Controllers\UserBookingsHistoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect(route('dashboard')));

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');

    // Members
    Route::prefix('members')->name('admin.members.')->group(function () {
        Route::get('/', [MembersController::class, 'index'])->name('index');
        Route::get('/create', [MembersController::class, 'create'])->name('create');
        Route::post('/store', [MembersController::class, 'store'])->name('store');
        Route::get('/{user}', [MembersController::class, 'show'])->name('show');
        Route::get('/{user}/history', UserBookingsHistoryController::class)->name('history');
    });

    // Trainers
    Route::prefix('trainers')->name('admin.trainers.')->group(function () {
        Route::get('/', [TrainersController::class, 'index'])->name('index');
        Route::get('/create', [TrainersController::class, 'create'])->name('create');
        Route::post('/store', [TrainersController::class, 'store'])->name('store');
        Route::get('/{user}', [TrainersController::class, 'show'])->name('show');
        Route::get('/{user}/history', UserBookingsHistoryController::class)->name('history');
    });


    // Bookings
    Route::get('/bookings/create', [BookingsController::class, 'create'])->name('admin.bookings.create');
    Route::post('/bookings/store', [BookingsController::class, 'store'])->name('admin.bookings.store');
    Route::get('/bookings/{booking}', [BookingsController::class, 'show'])->name('admin.bookings.show');

    // Bookings slots
    Route::get('/bookings-slots/{bookingSlot}/show', [BookingSlotsController::class, 'show'])->name('admin.bookings-slots.show');
    Route::get('/bookings-slots/{bookingSlot}/edit', [BookingSlotsController::class, 'edit'])->name('admin.bookings-slots.edit');
    Route::put('/bookings-slots/{bookingSlot}/update', [BookingSlotsController::class, 'update'])->name('admin.bookings-slots.update');
});
