<?php

use App\Enums\Status;
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

test('it cancels a booking slot', function () {
    $bookingSlot = BookingSlot::query()->first();

    actingAsAdmin()
        ->delete(route('admin.bookings-slots.cancel.destroy', $bookingSlot))
        ->assertRedirect(route('admin.bookings-slots.show', [
            'bookingSlot' => $bookingSlot->id,
            'booking_id' => $bookingSlot->booking_id,
        ]));

    $bookingSlot->refresh();

    expect($bookingSlot->status)->toBe(Status::Cancelled);
});
