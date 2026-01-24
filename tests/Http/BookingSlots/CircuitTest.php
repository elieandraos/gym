<?php

use App\Models\BookingSlot;
use App\Models\BookingSlotCircuit;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

describe('Circuit Management', function () {
    test('it creates a circuit with default name', function () {
        $bookingSlot = BookingSlot::query()->first();
        $initialCount = $bookingSlot->circuits()->count();

        actingAsAdmin()
            ->post(route('admin.bookings-slots.circuits.store', $bookingSlot))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        expect($bookingSlot->fresh()->circuits()->count())->toBe($initialCount + 1);

        $circuit = $bookingSlot->circuits()->latest()->first();
        expect($circuit->name)->toBe('Circuit '.($initialCount + 1));
    });

    test('it creates a circuit with custom name', function () {
        $bookingSlot = BookingSlot::query()->first();

        actingAsAdmin()
            ->post(route('admin.bookings-slots.circuits.store', $bookingSlot), [
                'name' => 'Upper Body',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $circuit = $bookingSlot->circuits()->latest()->first();
        expect($circuit->name)->toBe('Upper Body');
    });

    test('it updates circuit name', function () {
        $bookingSlot = BookingSlot::query()->first();
        $circuit = BookingSlotCircuit::factory()->create([
            'booking_slot_id' => $bookingSlot->id,
            'name' => 'Circuit 1',
        ]);

        actingAsAdmin()
            ->patch(route('admin.bookings-slots.circuits.update', [
                'bookingSlot' => $bookingSlot,
                'circuit' => $circuit,
            ]), [
                'name' => 'Lower Body',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        expect($circuit->fresh()->name)->toBe('Lower Body');
    });

    test('it validates circuit name is required for update', function () {
        $bookingSlot = BookingSlot::query()->first();
        $circuit = BookingSlotCircuit::factory()->create([
            'booking_slot_id' => $bookingSlot->id,
        ]);

        actingAsAdmin()
            ->patch(route('admin.bookings-slots.circuits.update', [
                'bookingSlot' => $bookingSlot,
                'circuit' => $circuit,
            ]), [
                'name' => '',
            ])
            ->assertSessionHasErrors(['name']);
    });

    test('it deletes a circuit', function () {
        $bookingSlot = BookingSlot::query()->first();
        $circuit = BookingSlotCircuit::factory()->create([
            'booking_slot_id' => $bookingSlot->id,
        ]);

        $circuitId = $circuit->id;

        actingAsAdmin()
            ->delete(route('admin.bookings-slots.circuits.destroy', [
                'bookingSlot' => $bookingSlot,
                'circuit' => $circuit,
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        expect(BookingSlotCircuit::find($circuitId))->toBeNull();
    });

    test('it requires authentication to manage circuits', function () {
        $bookingSlot = BookingSlot::query()->first();

        $this->post(route('admin.bookings-slots.circuits.store', $bookingSlot))
            ->assertRedirect(route('login'));
    });
});

describe('Circuit Workout Management', function () {
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

        $this->assertDatabaseHas('booking_slot_circuit_workouts', [
            'booking_slot_circuit_id' => $circuit->id,
            'workout_id' => $workout->id,
        ]);

        $circuitWorkout = $circuit->circuitWorkouts()->latest()->first();
        expect($circuitWorkout->sets)->toHaveCount(3);
        expect($circuitWorkout->sets[0]->reps)->toBe(12);
        expect($circuitWorkout->sets[0]->weight_in_kg)->toBe('20.00');
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

        $circuitWorkout = $circuit->circuitWorkouts()->latest()->first();
        expect($circuitWorkout->sets)->toHaveCount(3);
        expect($circuitWorkout->sets[0]->duration_in_seconds)->toBe(60);
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

    test('it requires authentication to manage circuit workouts', function () {
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
});
