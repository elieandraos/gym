<?php

namespace App\Actions\Admin;

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use Illuminate\Support\Facades\DB;

class CloneBookingSlotCircuit
{
    public function handle(BookingSlotCircuit $sourceCircuit, BookingSlot $targetBookingSlot): void
    {
        DB::transaction(function () use ($sourceCircuit, $targetBookingSlot) {
            $circuitCount = $targetBookingSlot->circuits()->count();
            /** @var BookingSlotCircuit $targetCircuit */
            $targetCircuit = $targetBookingSlot->circuits()->create([
                'name' => $sourceCircuit->name ?? 'Circuit '.($circuitCount + 1),
            ]);

            foreach ($sourceCircuit->circuitWorkouts()->with('sets')->get() as $sourceWorkout) {
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
        });
    }
}
