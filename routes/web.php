<?php

use App\Http\Controllers\Admin\BookingsController;
use App\Http\Controllers\Admin\BookingSlotsController;
use App\Http\Controllers\Admin\BookingSlotWorkoutController;
use App\Http\Controllers\Admin\CancelBookingSlotController;
use App\Http\Controllers\Admin\ChangeBookingSlotDateTimeController;
use App\Http\Controllers\Admin\MarkBookingAsPaidController;
use App\Http\Controllers\Admin\MemberBookingHistoryController;
use App\Http\Controllers\Admin\MembersController;
use App\Http\Controllers\Admin\TrainersController;
use App\Http\Controllers\Admin\WeeklyCalendarController;
use App\Http\Controllers\Admin\WorkoutController;
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
        Route::get('/{user}/bookings/history', [MemberBookingHistoryController::class, 'index'])->name('bookings.history');
    });

    // Trainers
    Route::prefix('trainers')->name('admin.trainers.')->group(function () {
        Route::get('/', [TrainersController::class, 'index'])->name('index');
        Route::get('/create', [TrainersController::class, 'create'])->name('create');
        Route::post('/store', [TrainersController::class, 'store'])->name('store');
        Route::get('/{user}', [TrainersController::class, 'show'])->name('show');
    });

    // Workouts
    Route::prefix('workouts')->name('admin.workouts.')->group(function () {
        Route::get('/', [WorkoutController::class, 'index'])->name('index');
        Route::get('/create', [WorkoutController::class, 'create'])->name('create');
        Route::post('/store', [WorkoutController::class, 'store'])->name('store');
        Route::get('/{workout}/edit', [WorkoutController::class, 'edit'])->name('edit');
        Route::put('/{workout}', [WorkoutController::class, 'update'])->name('update');
        Route::delete('/{workout}', [WorkoutController::class, 'destroy'])->name('destroy');
    });

    // Bookings
    Route::get('/bookings/create', [BookingsController::class, 'create'])->name('admin.bookings.create');
    Route::post('/bookings/store', [BookingsController::class, 'store'])->name('admin.bookings.store');
    Route::get('/bookings/{booking}', [BookingsController::class, 'show'])->name('admin.bookings.show');
    Route::patch('/bookings/{booking}/mark-as-paid', MarkBookingAsPaidController::class)->name('admin.bookings.mark-as-paid');

    // Bookings slot
    Route::get('/bookings-slots/{bookingSlot}/show', [BookingSlotsController::class, 'show'])->name('admin.bookings-slots.show');
    //  Bookings slot change date time
    Route::get('/bookings-slots/{bookingSlot}/change-date-time/edit', [ChangeBookingSlotDateTimeController::class, 'edit'])->name('admin.change-booking-slot-date-time.edit');
    Route::put('/bookings-slots/{bookingSlot}/change-date-time/update', [ChangeBookingSlotDateTimeController::class, 'update'])->name('admin.change-booking-slot-date-time.update');
    // Bookings slot cancel
    Route::get('/bookings-slots/{bookingSlot}/cancel', [CancelBookingSlotController::class, 'index'])->name('admin.bookings-slots.cancel.index');
    Route::delete('/bookings-slots/{bookingSlot}/cancel', [CancelBookingSlotController::class, 'destroy'])->name('admin.bookings-slots.cancel.destroy');
    // Bookings slot workouts
    Route::get('/bookings-slots/{bookingSlot}/workout/create', [BookingSlotWorkoutController::class, 'create'])->name('admin.bookings-slots.workout.create');
    Route::post('/bookings-slots/{bookingSlot}/workout/store', [BookingSlotWorkoutController::class, 'store'])->name('admin.bookings-slots.workout.store');
    Route::delete('/bookings-slots/workout/{bookingSlotWorkout}', [BookingSlotWorkoutController::class, 'destroy'])->name('admin.bookings-slots.workout.destroy');

    // Weekly Calendar
    Route::get('/calendar', [WeeklyCalendarController::class, 'index'])->name('admin.weekly-calendar.index');
});
