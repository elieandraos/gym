<?php

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    setupUsersAndBookings();
});

test('unfreeze index route requires authentication', function () {
    $booking = Booking::query()->first();

    $this->get(route('admin.bookings.unfreeze.index', $booking))
        ->assertRedirect(route('login'));
});

test('it renders the unfreeze booking page with frozen slots', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'is_frozen' => true,
        'frozen_at' => Carbon::now()->subDays(5),
    ]);

    BookingSlot::factory()->count(3)->create([
        'booking_id' => $booking->id,
        'status' => Status::Frozen,
        'start_time' => Carbon::now()->addDays(1),
        'end_time' => Carbon::now()->addDays(1)->addHour(),
    ]);

    $booking->load(['member', 'trainer', 'bookingSlots']);

    actingAsAdmin()
        ->get(route('admin.bookings.unfreeze.index', $booking))
        ->assertHasComponent('Admin/UnfreezeBooking/Index')
        ->assertStatus(200);
});
