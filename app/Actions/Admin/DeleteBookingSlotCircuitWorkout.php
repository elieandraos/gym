<?php

namespace App\Actions\Admin;

use App\Models\BookingSlotCircuitWorkout;

class DeleteBookingSlotCircuitWorkout
{
    public function handle(BookingSlotCircuitWorkout $circuitWorkout): void
    {
        $circuitWorkout->delete();
    }
}
