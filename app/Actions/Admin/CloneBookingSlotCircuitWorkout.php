<?php

namespace App\Actions\Admin;

use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;

class CloneBookingSlotCircuitWorkout
{
    public function handle(BookingSlotCircuitWorkout $sourceWorkout, BookingSlotCircuit $targetCircuit): void
    {
        app(CreateBookingSlotCircuitWorkout::class)->handle($targetCircuit, [
            'workout_id' => $sourceWorkout->workout_id,
            'notes' => $sourceWorkout->notes,
            'sets' => $sourceWorkout->sets->map(fn ($set) => [
                'reps' => $set->reps,
                'weight_in_kg' => $set->weight_in_kg,
                'duration_in_seconds' => $set->duration_in_seconds,
            ])->all(),
        ]);
    }
}
