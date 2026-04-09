<?php

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it creates a weight-based workout with sets', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $data = [
        'workout_id' => $workout->id,
        'type' => 'weight',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
            ['reps' => 10, 'weight_in_kg' => 22.5, 'duration_in_seconds' => null],
            ['reps' => 8, 'weight_in_kg' => 25, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it creates a duration-based workout with sets', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $data = [
        'workout_id' => $workout->id,
        'type' => 'duration',
        'sets' => [
            ['reps' => null, 'weight_in_kg' => null, 'duration_in_seconds' => 60],
            ['reps' => null, 'weight_in_kg' => null, 'duration_in_seconds' => 45],
            ['reps' => null, 'weight_in_kg' => null, 'duration_in_seconds' => 30],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it validates workout_id is required', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);

    $data = [
        'type' => 'weight',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasErrors(['workout_id']);
});

test('it validates workout_id exists', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);

    $data = [
        'workout_id' => 99999,
        'type' => 'weight',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasErrors(['workout_id']);
});

test('it validates type is required and valid', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $data = [
        'workout_id' => $workout->id,
        'type' => 'invalid',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasErrors(['type']);
});

test('it validates sets is required and is an array', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $data = [
        'workout_id' => $workout->id,
        'type' => 'weight',
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasErrors(['sets']);
});

test('it saves notes when creating a workout', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $data = [
        'workout_id' => $workout->id,
        'type' => 'weight',
        'notes' => 'Focus on slow negatives',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it creates a workout without notes', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $data = [
        'workout_id' => $workout->id,
        'type' => 'weight',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.circuits.workouts.store', [
            'bookingSlot' => $bookingSlot,
            'circuit' => $circuit,
        ]), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect();
});

test('it requires authentication to create circuit workouts', function () {
    $bookingSlot = BookingSlot::query()->first();
    $circuit = BookingSlotCircuit::factory()->create(['booking_slot_id' => $bookingSlot->id]);
    $workout = Workout::query()->first();

    $data = [
        'workout_id' => $workout->id,
        'type' => 'weight',
        'sets' => [
            ['reps' => 12, 'weight_in_kg' => 20, 'duration_in_seconds' => null],
        ],
    ];

    $this->post(route('admin.bookings-slots.circuits.workouts.store', [
        'bookingSlot' => $bookingSlot,
        'circuit' => $circuit,
    ]), $data)
        ->assertRedirect(route('login'));
});
