<?php

use App\Http\Controllers\Admin\BodyCompositionController;
use App\Http\Controllers\Admin\BookingsController;
use App\Http\Controllers\Admin\BookingSlotsController;
use App\Http\Controllers\Admin\BookingSlotWorkoutController;
use App\Http\Controllers\Admin\CancelBookingSlotController;
use App\Http\Controllers\Admin\ChangeBookingSlotDateTimeController;
use App\Http\Controllers\Admin\DailyCalendarController;
use App\Http\Controllers\Admin\FreezeBookingController;
use App\Http\Controllers\Admin\MarkBookingAsPaidController;
use App\Http\Controllers\Admin\MemberBookingHistoryController;
use App\Http\Controllers\Admin\MemberCreatedController;
use App\Http\Controllers\Admin\MemberPersonalInfoController;
use App\Http\Controllers\Admin\MembersController;
use App\Http\Controllers\Admin\TrainersController;
use App\Http\Controllers\Admin\UnfreezeBookingController;
use App\Http\Controllers\Admin\UserSettingsController;
use App\Http\Controllers\Admin\WeeklyCalendarController;
use App\Http\Controllers\Admin\WorkoutController;
use App\Http\Controllers\DashboardController;
use App\Mail\Member\BookingSlotReminderEmail;
use App\Mail\Member\WelcomeEmail;
use App\Mail\Owner\NewMemberEmail;
use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect(route('dashboard')));

// Email Previews (for development only)
Route::prefix('preview-emails')->group(function () {
    Route::get('/', function () {
        $type = request('type', 'member');

        return view('preview-emails.index', [
            'type' => $type,
        ]);
    });

    Route::get('/member/welcome', function () {
        $member = User::query()->members()->inRandomOrder()->first();

        return (new WelcomeEmail($member))->render();
    });

    Route::get('/member/booking-slot-reminder', function () {
        $bookingSlot = BookingSlot::query()
            ->with(['booking.member', 'booking.trainer'])
            ->inRandomOrder()
            ->first();

        return (new BookingSlotReminderEmail($bookingSlot))->render();
    });

    Route::get('/owner/new-member', function () {
        $member = User::query()->members()->inRandomOrder()->first();

        return (new NewMemberEmail($member))->render();
    });
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Members
    Route::prefix('members')->name('admin.members.')->group(function () {
        Route::get('/', [MembersController::class, 'index'])->name('index');
        Route::get('/create', [MembersController::class, 'create'])->name('create');
        Route::post('/store', [MembersController::class, 'store'])->name('store');
        Route::get('/{user}', [MembersController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [MembersController::class, 'edit'])->name('edit');
        Route::match(['put', 'post'], '/{user}', [MembersController::class, 'update'])->name('update');
        Route::get('/{user}/delete', [MembersController::class, 'delete'])->name('delete');
        Route::delete('/{user}', [MembersController::class, 'destroy'])->name('destroy');
        Route::get('/{user}/personal-info', [MemberPersonalInfoController::class, 'index'])->name('personal-info');
        Route::get('/{user}/bookings/history', [MemberBookingHistoryController::class, 'index'])->name('bookings.history');
        Route::get('/{user}/body-composition/create', [BodyCompositionController::class, 'create'])->name('body-composition.create');
        Route::post('/{user}/body-composition/store', [BodyCompositionController::class, 'store'])->name('body-composition.store');
    });

    // Member Created Success
    Route::get('/members/{user}/created', MemberCreatedController::class)->name('admin.member-created');

    // Trainers
    Route::prefix('trainers')->name('admin.trainers.')->group(function () {
        Route::get('/', [TrainersController::class, 'index'])->name('index');
        Route::get('/create', [TrainersController::class, 'create'])->name('create');
        Route::post('/store', [TrainersController::class, 'store'])->name('store');
        Route::get('/{user}', [TrainersController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [TrainersController::class, 'edit'])->name('edit');
        Route::match(['put', 'post'], '/{user}', [TrainersController::class, 'update'])->name('update');
    });

    // Settings
    Route::get('/settings', [UserSettingsController::class, 'edit'])->name('admin.settings.edit');
    Route::patch('/settings', [UserSettingsController::class, 'update'])->name('admin.settings.update');

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
    // Mark booking as paid
    Route::patch('/bookings/{booking}/mark-as-paid', MarkBookingAsPaidController::class)->name('admin.bookings.mark-as-paid');
    // Freeze/Unfreeze booking
    Route::get('/bookings/{booking}/freeze', [FreezeBookingController::class, 'index'])->name('admin.bookings.freeze.index');
    Route::patch('/bookings/{booking}/freeze', [FreezeBookingController::class, 'update'])->name('admin.bookings.freeze.update');
    Route::get('/bookings/{booking}/unfreeze', [UnfreezeBookingController::class, 'index'])->name('admin.bookings.unfreeze.index');
    Route::patch('/bookings/{booking}/unfreeze', [UnfreezeBookingController::class, 'update'])->name('admin.bookings.unfreeze.update');

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
    Route::delete('/bookings-slots/{bookingSlot}/workout/{bookingSlotWorkout}', [BookingSlotWorkoutController::class, 'destroy'])->name('admin.bookings-slots.workout.destroy');

    // Weekly Calendar
    Route::get('/weekly-calendar', [WeeklyCalendarController::class, 'index'])->name('admin.weekly-calendar.index');

    // Daily Calendar
    Route::get('/daily-calendar', [DailyCalendarController::class, 'index'])->name('admin.daily-calendar.index');
});
