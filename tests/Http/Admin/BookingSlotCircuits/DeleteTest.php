<?php

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it deletes a circuit', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create([
        'booking_slot_id' => $bookingSlot->id,
    ]);

    actingAsAdmin()
        ->delete(route('admin.bookings-slots.circuits.destroy', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});
