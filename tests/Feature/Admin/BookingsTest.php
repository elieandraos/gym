<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\Role;
use App\Http\Resources\BookingResource;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it requires authentication', function () {
    $booking = Booking::query()->first();

    $this->get(route('admin.bookings.show', $booking))->assertRedirect(route('login'));
});

test('it renders the create booking page', function () {
    actingAsAdmin()
        ->get(route('admin.bookings.create' ))
        ->assertHasComponent('Admin/Bookings/Create')
        ->assertStatus(200);
});

test('it validates request before creating a booking', function () {
    $data = [
        'member_id' => null,
        'trainer_id' => null,
        'start_date' => null,
        'nb_sessions' => null,
    ];

    actingAsAdmin()
        ->post(route('admin.bookings.store'), $data)
        ->assertSessionHasErrors([
            'member_id',
            'trainer_id',
            'start_date',
            'nb_sessions',
        ])
        ->assertStatus(302);
});


test('it shows booking information', function () {
    $booking = Booking::query()->first();
    $booking->load(['member', 'trainer', 'bookingSlots' => function ($query) {
        $query->orderBy('start_time');
    }]);

    actingAsAdmin()
        ->get(route('admin.bookings.show', $booking))
        ->assertHasComponent('Admin/Bookings/Show')
        ->assertHasResource('booking', BookingResource::make($booking))
        ->assertStatus(200);
});

test('it creates a booking', function () {
    $member = User::members()->inRandomOrder()->first();
    $trainer = User::trainers()->inRandomOrder()->first();

    $data = [
        'start_date' => Carbon::today(),
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'nb_sessions' => 12,
    ];

    actingAsAdmin()
        ->post(route('admin.bookings.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.show', ['role' => $member->role, 'user' => $member->id]));

    $this->assertDatabaseHas(Booking::class, $data);
});
