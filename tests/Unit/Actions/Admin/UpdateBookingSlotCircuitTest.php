<?php

use App\Actions\Admin\UpdateBookingSlotCircuit;
use App\Models\BookingSlotCircuit;

test('it updates the circuit name in the database', function () {
    $circuit = BookingSlotCircuit::factory()->named('Old Name')->create();

    app(UpdateBookingSlotCircuit::class)->handle($circuit, ['name' => 'New Name']);

    $this->assertDatabaseHas(BookingSlotCircuit::class, ['id' => $circuit->id, 'name' => 'New Name']);
});
