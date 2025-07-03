<?php

use App\Http\Resources\BookingSlotResource;
use App\Http\Resources\WorkoutResource;
use App\Models\BookingSlot;
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
    $bookingSlot->load(['booking', 'booking.member', 'booking.trainer']);

    $workouts = Workout::query()->orderBy('category')->orderBy('name')->get();

    actingAsAdmin()
        ->get(route('admin.bookings-slots.workout.create', $bookingSlot))
        ->assertHasComponent('Admin/BookingSlotWorkout/Create')
        ->assertHasResource('bookingSlot', BookingSlotResource::make($bookingSlot))
        ->assertHasResource('workouts', WorkoutResource::collection($workouts))
        ->assertStatus(200);
});
