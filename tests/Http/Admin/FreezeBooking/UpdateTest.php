<?php

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    setupUsersAndBookings();
});

test('freeze update route requires authentication', function () {
    $booking = Booking::query()->first();

    $this->patch(route('admin.bookings.freeze.update', $booking))
        ->assertRedirect(route('login'));
});

test('it freezes a booking successfully', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    BookingSlot::factory()->count(3)->create([
        'booking_id' => $booking->id,
        'status' => Status::Upcoming,
        'start_time' => Carbon::now()->addDays(1),
        'end_time' => Carbon::now()->addDays(1)->addHour(),
    ]);

    BookingSlot::factory()->count(2)->create([
        'booking_id' => $booking->id,
        'status' => Status::Complete,
        'start_time' => Carbon::now()->subDays(3),
        'end_time' => Carbon::now()->subDays(3)->addHour(),
    ]);

    actingAsAdmin()
        ->patch(route('admin.bookings.freeze.update', $booking))
        ->assertSessionHas('flash.banner', 'Booking frozen successfully')
        ->assertSessionHas('flash.bannerStyle', 'success')
        ->assertRedirect(route('admin.members.show', $member->id));
});

test('it cannot freeze an already frozen booking', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'is_frozen' => true,
        'frozen_at' => Carbon::now()->subDays(2),
    ]);

    actingAsAdmin()
        ->patch(route('admin.bookings.freeze.update', $booking))
        ->assertSessionHas('flash.banner', 'This booking is already frozen')
        ->assertSessionHas('flash.bannerStyle', 'danger')
        ->assertRedirect();
});
