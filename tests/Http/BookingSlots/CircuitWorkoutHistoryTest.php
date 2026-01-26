<?php

use App\Models\Booking;
use App\Models\BookingSlot;

beforeEach(function () {
    setupUsersAndBookings();
});

test('route requires authentication', function () {
    $bookingSlot = BookingSlot::query()->first();

    $this->get(route('admin.bookings-slots.circuit-workout-history', $bookingSlot))
        ->assertRedirect(route('login'));
});

test('it returns previous sessions for the booking', function () {
    // Get a booking with at least 4 slots from seeded data
    /** @var Booking $booking */
    $booking = Booking::query()
        ->has('bookingSlots', '>=', 4)
        ->first();

    // Sort slots by start_time for deterministic ordering
    $slots = $booking->bookingSlots()->orderBy('start_time')->get();
    $currentSlot = $slots->last();
    $previousSlots = $slots->slice(0, -1);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', $currentSlot))
        ->assertStatus(200);

    $responseData = $response->json('previousSessions');

    // Default limit is 3
    expect($responseData)->toHaveCount(min(3, $previousSlots->count()));

    // Sessions should be ordered by start_time desc (most recent first)
    $returnedIds = collect($responseData)->pluck('id')->toArray();
    $expectedIds = $previousSlots->sortByDesc('start_time')->take(3)->pluck('id')->toArray();
    expect($returnedIds)->toBe($expectedIds);
});

test('it respects the limit parameter', function () {
    // Get a booking with at least 4 slots from seeded data
    /** @var Booking $booking */
    $booking = Booking::query()
        ->has('bookingSlots', '>=', 4)
        ->first();

    $slots = $booking->bookingSlots()->orderBy('start_time')->get();
    $currentSlot = $slots->last();

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', [
            'bookingSlot' => $currentSlot->id,
            'limit' => 2,
        ]))
        ->assertStatus(200);

    $responseData = $response->json('previousSessions');
    expect($responseData)->toHaveCount(2);
});

test('it only returns sessions before current slot', function () {
    // Get a booking with at least 5 slots from seeded data
    /** @var Booking $booking */
    $booking = Booking::query()
        ->has('bookingSlots', '>=', 5)
        ->first();

    // Sort slots by start_time and pick a middle slot as "current"
    $slots = $booking->bookingSlots()->orderBy('start_time')->get();
    $middleIndex = (int) floor($slots->count() / 2);
    $currentSlot = $slots->get($middleIndex);

    // Slots before the current one
    $previousSlots = $slots->slice(0, $middleIndex);
    // Slots after the current one
    $futureSlots = $slots->slice($middleIndex + 1);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', $currentSlot))
        ->assertStatus(200);

    $responseData = $response->json('previousSessions');

    // Should return up to 3 previous sessions (or fewer if less exist)
    expect($responseData)->toHaveCount(min(3, $previousSlots->count()));

    // Verify only previous slots are returned, not future or current
    $returnedIds = collect($responseData)->pluck('id')->toArray();
    foreach ($returnedIds as $id) {
        expect($previousSlots->pluck('id')->toArray())->toContain($id);
    }
    expect($returnedIds)->not->toContain($currentSlot->id);
    foreach ($futureSlots as $futureSlot) {
        expect($returnedIds)->not->toContain($futureSlot->id);
    }
});
