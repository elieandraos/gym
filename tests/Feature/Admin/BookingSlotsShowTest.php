<?php

use App\Http\Resources\BookingSlotResource;
use App\Models\BookingSlot;
use App\Models\BookingSlotWorkout;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it shows workout details on booking slot show page', function () {
    $bookingSlot = BookingSlot::query()->first();
    $workout = Workout::factory()->create();
    $bookingSlotWorkout = BookingSlotWorkout::query()->create([
        'booking_slot_id' => $bookingSlot->id,
        'workout_id' => $workout->id,
    ]);
    $bookingSlotWorkout->sets()->createMany([
        ['is_weighted' => true, 'is_timed' => false, 'weight_in_kg' => 10],
        ['is_weighted' => true, 'is_timed' => false, 'weight_in_kg' => 12],
    ]);

    $bookingSlot->load([
        'booking', 'booking.member', 'booking.trainer',
        'bookingSlotWorkouts.workout', 'bookingSlotWorkouts.sets',
    ]);

    actingAsAdmin()
        ->get(route('admin.bookings-slots.show', $bookingSlot))
        ->assertHasComponent('Admin/BookingsSlots/Show')
        ->assertHasResource('bookingSlot', BookingSlotResource::make($bookingSlot))
        ->assertHasProp('bookingSlot.workouts.0.name', $workout->name)
        ->assertStatus(200);
});
