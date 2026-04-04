<?php

use App\Actions\Admin\DeleteBookingSlotCircuit;
use App\Models\BookingSlotCircuit;

test('it deletes the circuit from the database', function () {
    $circuit = BookingSlotCircuit::factory()->create();

    (new DeleteBookingSlotCircuit)->handle($circuit);

    $this->assertDatabaseMissing(BookingSlotCircuit::class, ['id' => $circuit->id]);
});
