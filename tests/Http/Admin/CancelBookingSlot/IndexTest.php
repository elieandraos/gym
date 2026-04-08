<?php

use App\Models\BookingSlot;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it loads cancel booking slot page', function () {
    $bookingSlot = BookingSlot::query()->first();

    actingAsAdmin()
        ->get(route('admin.bookings-slots.cancel.index', $bookingSlot))
        ->assertHasComponent('Admin/CancelBookingSlot/Index')
        ->assertStatus(200);
});
