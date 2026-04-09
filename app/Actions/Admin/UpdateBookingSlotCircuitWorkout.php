<?php

namespace App\Actions\Admin;

use App\Models\BookingSlotCircuitWorkout;
use Illuminate\Support\Facades\DB;

class UpdateBookingSlotCircuitWorkout
{
    public function handle(BookingSlotCircuitWorkout $circuitWorkout, array $attributes): void
    {
        DB::transaction(function () use ($circuitWorkout, $attributes) {
            $circuitWorkout->update([
                'workout_id' => $attributes['workout_id'],
                'notes' => $attributes['notes'] ?? null,
            ]);

            $circuitWorkout->sets()->delete();

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
