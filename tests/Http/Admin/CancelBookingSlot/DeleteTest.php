<?php

use App\Models\BookingSlot;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it cancels a booking slot', function () {
    $bookingSlot = BookingSlot::query()->first();

    actingAsAdmin()
        ->delete(route('admin.bookings-slots.cancel.destroy', $bookingSlot))
        ->assertRedirect(route('admin.bookings-slots.show', [
            'bookingSlot' => $bookingSlot->id,
            'booking_id' => $bookingSlot->booking_id,
        ]));
});
