<?php

use App\Enums\Role;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\BookingSlotCircuitWorkout;
use App\Models\BookingSlotCircuitWorkoutSet;
use App\Models\User;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('route requires authentication', function () {
    $bookingSlot = BookingSlot::query()->first();

    $this->get(route('admin.bookings-slots.last-workout-result', $bookingSlot))
        ->assertRedirect(route('login'));
});

test('it returns null sets when the member has no previous occurrence of the workout', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);

    $workout = Workout::factory()->create();

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.last-workout-result', [
            'bookingSlot' => $currentSlot->id,
            'workout_id' => $workout->id,
        ]))
        ->assertSuccessful();

    expect($response->json('sets'))->toBeNull();
});

test('it returns sets from the most recent previous slot when the workout was done before', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $workout = Workout::factory()->create();

    $previousSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->subDays(7),
        'end_time' => now()->subDays(7)->addHour(),
    ]);

    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $previousSlot->id]);
    $circuitWorkout = BookingSlotCircuitWorkout::factory()->create([
        'booking_slot_circuit_id' => $circuit->id,
        'workout_id' => $workout->id,
    ]);

    $set1 = BookingSlotCircuitWorkoutSet::factory()->weighted()->create([
        'booking_slot_circuit_workout_id' => $circuitWorkout->id,
        'reps' => 10,
        'weight_in_kg' => 20,
    ]);

    $set2 = BookingSlotCircuitWorkoutSet::factory()->weighted()->create([
        'booking_slot_circuit_workout_id' => $circuitWorkout->id,
        'reps' => 8,
        'weight_in_kg' => 22.5,
    ]);

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.last-workout-result', [
            'bookingSlot' => $currentSlot->id,
            'workout_id' => $workout->id,
        ]))
        ->assertSuccessful();

    $returnedSets = $response->json('sets');

    expect($returnedSets)->toHaveCount(2)
        ->and($returnedSets[0]['id'])->toBe($set1->id)
        ->and($returnedSets[1]['id'])->toBe($set2->id);
});

test('it does not return results from a future or current slot', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $workout = Workout::factory()->create();

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDays(1),
        'end_time' => now()->addDays(1)->addHour(),
    ]);

    $futureSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDays(3),
        'end_time' => now()->addDays(3)->addHour(),
    ]);

    $futureCircuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $futureSlot->id]);
    $futureCircuitWorkout = BookingSlotCircuitWorkout::factory()->create([
        'booking_slot_circuit_id' => $futureCircuit->id,
        'workout_id' => $workout->id,
    ]);
    BookingSlotCircuitWorkoutSet::factory()->weighted()->create([
        'booking_slot_circuit_workout_id' => $futureCircuitWorkout->id,
    ]);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.last-workout-result', [
            'bookingSlot' => $currentSlot->id,
            'workout_id' => $workout->id,
        ]))
        ->assertSuccessful();

    expect($response->json('sets'))->toBeNull();
});

test('it returns the most recent previous result when multiple past occurrences exist', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $workout = Workout::factory()->create();

    $olderSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->subDays(14),
        'end_time' => now()->subDays(14)->addHour(),
    ]);
    $olderCircuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $olderSlot->id]);
    $olderCircuitWorkout = BookingSlotCircuitWorkout::factory()->create([
        'booking_slot_circuit_id' => $olderCircuit->id,
        'workout_id' => $workout->id,
    ]);
    BookingSlotCircuitWorkoutSet::factory()->weighted()->create([
        'booking_slot_circuit_workout_id' => $olderCircuitWorkout->id,
        'reps' => 5,
        'weight_in_kg' => 10,
    ]);

    $recentSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->subDays(3),
        'end_time' => now()->subDays(3)->addHour(),
    ]);
    $recentCircuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $recentSlot->id]);
    $recentCircuitWorkout = BookingSlotCircuitWorkout::factory()->create([
        'booking_slot_circuit_id' => $recentCircuit->id,
        'workout_id' => $workout->id,
    ]);
    $recentSet = BookingSlotCircuitWorkoutSet::factory()->weighted()->create([
        'booking_slot_circuit_workout_id' => $recentCircuitWorkout->id,
        'reps' => 12,
        'weight_in_kg' => 25,
    ]);

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.last-workout-result', [
            'bookingSlot' => $currentSlot->id,
            'workout_id' => $workout->id,
        ]))
        ->assertSuccessful();

    $returnedSets = $response->json('sets');

    expect($returnedSets)->toHaveCount(1)
        ->and($returnedSets[0]['id'])->toBe($recentSet->id);
});
