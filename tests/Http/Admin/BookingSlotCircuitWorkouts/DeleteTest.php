<?php

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it deletes a circuit workout', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $circuitWorkout = $circuit->circuitWorkouts()->create(['workout_id' => $workout->id]);
    $circuitWorkout->sets()->create([
        'reps' => 12,
        'weight_in_kg' => 20,
        'duration_in_seconds' => null,
    ]);

    $circuitWorkoutId = $circuitWorkout->id;

    actingAsAdmin()
        ->delete(route('admin.bookings-slots.circuits.workouts.destroy', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
            'circuitWorkout' => $circuitWorkout,
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $this->assertDatabaseMissing('booking_slot_circuit_workouts', ['id' => $circuitWorkoutId]);
});
