<?php

use App\Actions\Admin\DeleteBookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuitWorkout;

test('it deletes the circuit workout from the database', function () {
    $circuitWorkout = BookingSlotCircuitWorkout::factory()->create();

    (new DeleteBookingSlotCircuitWorkout)->handle($circuitWorkout);

    $this->assertDatabaseMissing(BookingSlotCircuitWorkout::class, ['id' => $circuitWorkout->id]);
});
