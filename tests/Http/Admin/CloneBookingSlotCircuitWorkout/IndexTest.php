<?php

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuitWorkoutSet;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it clones a workout into an existing circuit', function () {
    $targetBookingSlot = BookingSlot::query()->first();
    $targetCircuit = BookingSlotCircuit::factory()->for($targetBookingSlot, 'bookingSlot')->create();
    $sourceWorkout = BookingSlotCircuitWorkout::factory()->create();
    BookingSlotCircuitWorkoutSet::factory()->weighted()->for($sourceWorkout, 'circuitWorkout')->count(2)->create();

    actingAsAdmin()
        ->post(route('admin.bookings-slots.clone-circuit-workout', $targetBookingSlot), [
            'source_workout_id' => $sourceWorkout->id,
            'circuit_id' => $targetCircuit->id,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it clones a workout into a new circuit when circuit_id is null', function () {
    $targetBookingSlot = BookingSlot::query()->first();
    $sourceWorkout = BookingSlotCircuitWorkout::factory()->create();
    BookingSlotCircuitWorkoutSet::factory()->weighted()->for($sourceWorkout, 'circuitWorkout')->count(1)->create();

    actingAsAdmin()
        ->post(route('admin.bookings-slots.clone-circuit-workout', $targetBookingSlot), [
            'source_workout_id' => $sourceWorkout->id,
            'circuit_id' => null,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it requires source_workout_id', function () {
    $bookingSlot = BookingSlot::query()->first();

    actingAsAdmin()
        ->post(route('admin.bookings-slots.clone-circuit-workout', $bookingSlot), [])
        ->assertSessionHasErrors(['source_workout_id']);
});

test('it requires authentication', function () {
    $bookingSlot = BookingSlot::query()->first();
    $sourceWorkout = BookingSlotCircuitWorkout::factory()->create();

    $this->post(route('admin.bookings-slots.clone-circuit-workout', $bookingSlot), [
        'source_workout_id' => $sourceWorkout->id,
    ])->assertRedirect(route('login'));
});
