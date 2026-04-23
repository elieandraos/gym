<?php

use App\Actions\Admin\CloneBookingSlotCircuit;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuitWorkoutSet;

test('it clones a circuit with its name into the target booking slot', function () {
    $sourceCircuit = BookingSlotCircuit::factory()->named('Push Day')->create();
    $targetBookingSlot = BookingSlot::factory()->create();

    app(CloneBookingSlotCircuit::class)->handle($sourceCircuit, $targetBookingSlot);

    $this->assertDatabaseHas(BookingSlotCircuit::class, [
        'booking_slot_id' => $targetBookingSlot->id,
        'name' => 'Push Day',
    ]);
});

test('it clones all workouts with their sets', function () {
    $sourceCircuit = BookingSlotCircuit::factory()->create();
    $workout = BookingSlotCircuitWorkout::factory()->for($sourceCircuit, 'circuit')->create(['notes' => 'Keep elbows tight']);
    BookingSlotCircuitWorkoutSet::factory()->weighted()->for($workout, 'circuitWorkout')->count(3)->create();

    $targetBookingSlot = BookingSlot::factory()->create();

    app(CloneBookingSlotCircuit::class)->handle($sourceCircuit, $targetBookingSlot);

    $clonedCircuit = $targetBookingSlot->circuits()->with('circuitWorkouts.sets')->first();

    expect($clonedCircuit->circuitWorkouts)->toHaveCount(1)
        ->and($clonedCircuit->circuitWorkouts->first()->notes)->toBe('Keep elbows tight')
        ->and($clonedCircuit->circuitWorkouts->first()->sets)->toHaveCount(3);
});

test('it does not modify the source circuit', function () {
    $sourceCircuit = BookingSlotCircuit::factory()->create();
    $workout = BookingSlotCircuitWorkout::factory()->for($sourceCircuit, 'circuit')->create();
    BookingSlotCircuitWorkoutSet::factory()->weighted()->for($workout, 'circuitWorkout')->count(2)->create();

    $targetBookingSlot = BookingSlot::factory()->create();

    app(CloneBookingSlotCircuit::class)->handle($sourceCircuit, $targetBookingSlot);

    expect($sourceCircuit->fresh()->circuitWorkouts()->count())->toBe(1)
        ->and($sourceCircuit->fresh()->circuitWorkouts()->first()->sets()->count())->toBe(2);
});
