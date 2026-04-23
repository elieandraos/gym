<?php

use App\Actions\Admin\CloneBookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuitWorkoutSet;

test('it clones a workout with its sets into the target circuit', function () {
    $sourceWorkout = BookingSlotCircuitWorkout::factory()->create(['notes' => 'Slow and controlled']);
    BookingSlotCircuitWorkoutSet::factory()->weighted()->for($sourceWorkout, 'circuitWorkout')->count(3)->create();
    $sourceWorkout->load('sets');

    $targetCircuit = BookingSlotCircuit::factory()->create();

    app(CloneBookingSlotCircuitWorkout::class)->handle($sourceWorkout, $targetCircuit);

    $clonedWorkout = $targetCircuit->circuitWorkouts()->with('sets')->first();

    expect($clonedWorkout)->not->toBeNull()
        ->and($clonedWorkout->workout_id)->toBe($sourceWorkout->workout_id)
        ->and($clonedWorkout->notes)->toBe('Slow and controlled')
        ->and($clonedWorkout->sets)->toHaveCount(3);
});

test('it clones timed sets correctly', function () {
    $sourceWorkout = BookingSlotCircuitWorkout::factory()->create();
    BookingSlotCircuitWorkoutSet::factory()->timed()->for($sourceWorkout, 'circuitWorkout')->count(2)->create();
    $sourceWorkout->load('sets');

    $targetCircuit = BookingSlotCircuit::factory()->create();

    app(CloneBookingSlotCircuitWorkout::class)->handle($sourceWorkout, $targetCircuit);

    $clonedSets = $targetCircuit->circuitWorkouts()->first()->sets;

    expect($clonedSets)->toHaveCount(2);
    $clonedSets->each(function ($set) {
        expect($set->duration_in_seconds)->not->toBeNull()
            ->and($set->weight_in_kg)->toBeNull();
    });
});

test('it does not modify the source workout', function () {
    $sourceWorkout = BookingSlotCircuitWorkout::factory()->create();
    BookingSlotCircuitWorkoutSet::factory()->weighted()->for($sourceWorkout, 'circuitWorkout')->count(2)->create();
    $sourceWorkout->load('sets');

    $targetCircuit = BookingSlotCircuit::factory()->create();

    app(CloneBookingSlotCircuitWorkout::class)->handle($sourceWorkout, $targetCircuit);

    expect($sourceWorkout->fresh()->sets()->count())->toBe(2);
});
