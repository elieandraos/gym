<?php

use App\Actions\Admin\UpdateBookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuitWorkout;
use App\Models\Workout;

test('it updates the workout and replaces sets', function () {
    $circuitWorkout = BookingSlotCircuitWorkout::factory()->create();
    $circuitWorkout->sets()->create(['reps' => 5, 'weight_in_kg' => 20, 'duration_in_seconds' => null]);

    $newWorkout = Workout::factory()->create();

    (new UpdateBookingSlotCircuitWorkout)->handle($circuitWorkout, [
        'workout_id' => $newWorkout->id,
        'notes' => 'Updated notes',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 60, 'duration_in_seconds' => null],
            ['reps' => 10, 'weight_in_kg' => 65, 'duration_in_seconds' => null],
            ['reps' => 8, 'weight_in_kg' => 70, 'duration_in_seconds' => null],
        ],
    ]);

    expect($circuitWorkout->fresh()->workout_id)->toBe($newWorkout->id)
        ->and($circuitWorkout->fresh()->notes)->toBe('Updated notes')
        ->and($circuitWorkout->sets()->count())->toBe(3);
});
