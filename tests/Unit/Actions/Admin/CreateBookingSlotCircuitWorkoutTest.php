<?php

use App\Actions\Admin\CreateBookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use App\Models\Workout;

test('it creates a circuit workout with all sets', function () {
    $circuit = BookingSlotCircuit::factory()->create();
    $workout = Workout::factory()->create();

    (new CreateBookingSlotCircuitWorkout)->handle($circuit, [
        'workout_id' => $workout->id,
        'notes' => 'Focus on form',
        'sets' => [
            ['reps' => 10, 'weight_in_kg' => 50, 'duration_in_seconds' => null],
            ['reps' => 8, 'weight_in_kg' => 55, 'duration_in_seconds' => null],
        ],
    ]);

    $circuitWorkout = BookingSlotCircuitWorkout::query()
        ->where('booking_slot_circuit_id', $circuit->id)
        ->where('workout_id', $workout->id)
        ->first();

    expect($circuitWorkout)->not->toBeNull()
        ->and($circuitWorkout->notes)->toBe('Focus on form')
        ->and($circuitWorkout->sets)->toHaveCount(2);
});
