<?php

namespace App\Actions\Admin;

use App\Models\BookingSlotCircuit;
use Illuminate\Support\Facades\DB;

class CreateBookingSlotCircuitWorkout
{
    public function handle(BookingSlotCircuit $circuit, array $attributes): void
    {
        DB::transaction(function () use ($circuit, $attributes) {
            $circuitWorkout = $circuit->circuitWorkouts()->create([
                'workout_id' => $attributes['workout_id'],
                'notes' => $attributes['notes'] ?? null,
            ]);

            foreach ($attributes['sets'] as $setData) {
                $circuitWorkout->sets()->create([
                    'reps' => $setData['reps'] ?? null,
                    'weight_in_kg' => $setData['weight_in_kg'] ?? null,
                    'duration_in_seconds' => $setData['duration_in_seconds'] ?? null,
                ]);
            }
        });
    }
}
