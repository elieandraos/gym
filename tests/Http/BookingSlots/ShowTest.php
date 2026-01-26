<?php

use App\Http\Resources\BookingResource;
use App\Http\Resources\BookingSlotResource;
use App\Http\Resources\WorkoutResource;
use App\Models\BookingSlot;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('show route requires authentication', function () {
    $bookingSlot = BookingSlot::query()->first();

    $this->get(route('admin.bookings-slots.show', $bookingSlot))->assertRedirect(route('login'));
});

test('it shows booking slot information', function () {
    $bookingSlot = BookingSlot::query()->first();
    $bookingSlot->load([
        'booking.member',
        'booking.trainer',
        'circuits' => fn ($query) => $query->orderBy('created_at'),
        'circuits.circuitWorkouts.workout',
        'circuits.circuitWorkouts.sets' => fn ($query) => $query->orderBy('id'),
    ]);

    $workouts = Workout::query()->orderBy('name')->get();

    actingAsAdmin()
        ->get(route('admin.bookings-slots.show', $bookingSlot))
        ->assertHasComponent('Admin/BookingsSlots/Show')
        ->assertHasResource('bookingSlot', BookingSlotResource::make($bookingSlot))
        ->assertHasResource('booking', BookingResource::make($bookingSlot->booking))
        ->assertHasResource('workouts', WorkoutResource::collection($workouts))
        ->assertStatus(200);
});
