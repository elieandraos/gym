<?php

use App\Models\BookingSlot;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it creates a circuit with default name', function () {
    $bookingSlot = BookingSlot::query()->first();

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.store', $bookingSlot))
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it creates a circuit with custom name', function () {
    $bookingSlot = BookingSlot::query()->first();

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.store', $bookingSlot), [
            'name' => 'Upper Body',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it requires authentication to create circuits', function () {
    $bookingSlot = BookingSlot::query()->first();

    $this->post(route('admin.bookings-slots.circuits.store', $bookingSlot))
        ->assertRedirect(route('login'));
});
