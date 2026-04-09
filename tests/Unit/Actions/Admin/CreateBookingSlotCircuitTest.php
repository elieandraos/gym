<?php

use App\Actions\Admin\CreateBookingSlotCircuit;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;

test('it creates a circuit with the provided name', function () {
    $bookingSlot = BookingSlot::factory()->create();

    app(CreateBookingSlotCircuit::class)->handle($bookingSlot, ['name' => 'My Circuit']);

    $this->assertDatabaseHas(BookingSlotCircuit::class, [
        'booking_slot_id' => $bookingSlot->id,
        'name' => 'My Circuit',
    ]);
});

test('it auto-generates a name when none is provided', function () {
    $bookingSlot = BookingSlot::factory()->create();
    BookingSlotCircuit::factory()->for($bookingSlot, 'bookingSlot')->create();

    app(CreateBookingSlotCircuit::class)->handle($bookingSlot, []);

    $this->assertDatabaseHas(BookingSlotCircuit::class, [
        'booking_slot_id' => $bookingSlot->id,
        'name' => 'Circuit 2',
    ]);
});
