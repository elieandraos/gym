<?php

use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    setupUsersAndBookings();
});

test('freeze index route requires authentication', function () {
    $booking = Booking::query()->first();

    $this->get(route('admin.bookings.freeze.index', $booking))
        ->assertRedirect(route('login'));
});

test('it renders the freeze booking confirmation page', function () {
    $member = User::query()->members()->inRandomOrder()->first();
    $trainer = User::query()->trainers()->inRandomOrder()->first();

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $booking->load(['member.memberActiveBooking', 'trainer']);

    actingAsAdmin()
        ->get(route('admin.bookings.freeze.index', $booking))
        ->assertHasComponent('Admin/FreezeBooking/Index')
        ->assertStatus(200);
});
