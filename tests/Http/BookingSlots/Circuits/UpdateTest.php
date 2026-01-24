<?php

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it updates circuit name', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create([
        'booking_slot_id' => $bookingSlot->id,
        'name' => 'Circuit 1',
    ]);

    actingAsAdmin()
        ->patch(route('admin.bookings-slots.circuits.update', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), [
            'name' => 'Lower Body',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect($circuit->fresh()->name)->toBe('Lower Body');
});

test('it validates circuit name is required for update', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create([
        'booking_slot_id' => $bookingSlot->id,
    ]);

    actingAsAdmin()
        ->patch(route('admin.bookings-slots.circuits.update', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), [
            'name' => '',
        ])
        ->assertSessionHasErrors(['name']);
});
