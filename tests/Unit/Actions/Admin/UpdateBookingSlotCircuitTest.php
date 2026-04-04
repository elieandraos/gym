<?php

use App\Actions\Admin\UpdateBookingSlotCircuit;
use App\Models\BookingSlotCircuit;

test('it updates the circuit name in the database', function () {
    $circuit = BookingSlotCircuit::factory()->named('Old Name')->create();

    (new UpdateBookingSlotCircuit)->handle($circuit, ['name' => 'New Name']);

    $this->assertDatabaseHas(BookingSlotCircuit::class, ['id' => $circuit->id, 'name' => 'New Name']);
});
