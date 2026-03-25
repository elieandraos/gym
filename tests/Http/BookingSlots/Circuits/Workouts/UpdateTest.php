<?php

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it updates a circuit workout', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();
    $newWorkout = Workout::query()->skip(1)->first();

    // Create initial workout with sets
    $circuitWorkout = $circuit->circuitWorkouts()->create(['workout_id' => $workout->id]);
    $circuitWorkout->sets()->create([
        'reps' => 12,
        'weight_in_kg' => 20,
        'duration_in_seconds' => null,
    ]);

    // Update data
    $data = [
        'workout_id' => $newWorkout->id,
        'type' => 'weight',
        'sets' => [
            ['reps' => 10, 'weight_in_kg' => 25, 'duration_in_seconds' => null],
            ['reps' => 8, 'weight_in_kg' => 30, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->put(route('admin.bookings-slots.circuits.workouts.update', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
            'circuitWorkout' => $circuitWorkout,
        ]), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $circuitWorkout->refresh();
    expect($circuitWorkout->workout_id)->toBe($newWorkout->id);
    expect($circuitWorkout->sets)->toHaveCount(2);
    expect($circuitWorkout->sets[0]->reps)->toBe(10);
    expect($circuitWorkout->sets[0]->weight_in_kg)->toBe('25.00');
});

test('it saves notes when updating a workout', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $circuitWorkout = $circuit->circuitWorkouts()->create(['workout_id' => $workout->id]);
    $circuitWorkout->sets()->create(['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null]);

    $data = [
        'workout_id' => $workout->id,
        'type' => 'weight',
        'notes' => 'Keep elbows tucked',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->put(route('admin.bookings-slots.circuits.workouts.update', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
            'circuitWorkout' => $circuitWorkout,
        ]), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $circuitWorkout->refresh();
    expect($circuitWorkout->notes)->toBe('Keep elbows tucked');
});

test('it clears notes when updating without notes', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $circuitWorkout = $circuit->circuitWorkouts()->create([
        'workout_id' => $workout->id,
        'notes' => 'Old note',
    ]);
    $circuitWorkout->sets()->create(['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null]);

    $data = [
        'workout_id' => $workout->id,
        'type' => 'weight',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->put(route('admin.bookings-slots.circuits.workouts.update', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
            'circuitWorkout' => $circuitWorkout,
        ]), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $circuitWorkout->refresh();
    expect($circuitWorkout->notes)->toBeNull();
});
