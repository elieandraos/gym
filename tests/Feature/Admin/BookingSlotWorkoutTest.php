<?php

use App\Http\Resources\BookingSlotResource;
use App\Http\Resources\WorkoutResource;
use App\Models\BookingSlot;
use App\Models\BookingSlotWorkout;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
    Workout::factory()->count(5)->create();
});

test('it requires authentication', function () {
    $bookingSlot = BookingSlot::query()->first();

    $this->get(route('admin.bookings-slots.workout.create', $bookingSlot))
        ->assertRedirect(route('login'));
});

test('it renders the booking slot workout create page', function () {
    $bookingSlot = BookingSlot::query()->first();
    $bookingSlot->load(['booking', 'booking.member']);

    $workouts = Workout::query()->orderBy('category')->orderBy('name')->get();

    actingAsAdmin()
        ->get(route('admin.bookings-slots.workout.create', $bookingSlot))
        ->assertHasComponent('Admin/BookingSlotWorkout/Create')
        ->assertHasResource('bookingSlot', BookingSlotResource::make($bookingSlot))
        ->assertHasResource('workouts', WorkoutResource::collection($workouts))
        ->assertStatus(200);
});

test('it creates booking slot workouts and sets', function () {
    $bookingSlot = BookingSlot::query()->first();
    $workout1 = Workout::factory()->create();
    $workout2 = Workout::factory()->create();

    $data = [
        'workouts' => [
            [
                'id' => $workout1->id,
                'type' => 'weight',
                'weight_in_kg' => [10, 12, 14],
                'duration_in_seconds' => ['', '', ''],
            ],
            [
                'id' => $workout2->id,
                'type' => 'seconds',
                'weight_in_kg' => ['', '', ''],
                'duration_in_seconds' => [30, 40, 50],
            ],
        ],
    ];

    actingAsAdmin()
        ->post(route('admin.bookings-slots.workout.store', $bookingSlot), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.bookings-slots.show', $bookingSlot->id));

    $this->assertDatabaseHas(BookingSlotWorkout::class, [
        'booking_slot_id' => $bookingSlot->id,
        'workout_id' => $workout1->id,
    ]);

    $this->assertDatabaseHas(BookingSlotWorkout::class, [
        'booking_slot_id' => $bookingSlot->id,
        'workout_id' => $workout2->id,
    ]);

    $bookingSlotWorkout = BookingSlotWorkout::query()->where('booking_slot_id', $bookingSlot->id)
        ->where('workout_id', $workout1->id)
        ->first();

    expect($bookingSlotWorkout->sets()->count())->toBe(3);
});
