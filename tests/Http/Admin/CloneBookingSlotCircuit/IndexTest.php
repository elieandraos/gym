<?php

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it clones a circuit into the target booking slot', function () {
    $targetBookingSlot = BookingSlot::query()->first();
    $sourceCircuit = BookingSlotCircuit::factory()->create(['name' => 'Leg Day']);

    actingAsAdmin()
        ->post(route('admin.bookings-slots.clone-circuit', $targetBookingSlot), [
            'source_circuit_id' => $sourceCircuit->id,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it requires source_circuit_id', function () {
    $bookingSlot = BookingSlot::query()->first();

    actingAsAdmin()
        ->post(route('admin.bookings-slots.clone-circuit', $bookingSlot), [])
        ->assertSessionHasErrors(['source_circuit_id']);
});

test('it requires authentication', function () {
    $bookingSlot = BookingSlot::query()->first();
    $sourceCircuit = BookingSlotCircuit::factory()->create();

    $this->post(route('admin.bookings-slots.clone-circuit', $bookingSlot), [
        'source_circuit_id' => $sourceCircuit->id,
    ])->assertRedirect(route('login'));
});
