<?php

use App\Enums\Role;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('route requires authentication', function () {
    $bookingSlot = BookingSlot::query()->first();

    $this->get(route('admin.bookings-slots.circuit-workout-history', $bookingSlot))
        ->assertRedirect(route('login'));
});

test('it returns previous sessions across all member bookings', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $pastBooking = Booking::factory()->completed()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $currentBooking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $pastSlots = collect([
        BookingSlot::factory()->create([
            'booking_id' => $pastBooking->id,
            'start_time' => now()->subDays(10),
            'end_time' => now()->subDays(10)->addHour(),
        ]),
        BookingSlot::factory()->create([
            'booking_id' => $pastBooking->id,
            'start_time' => now()->subDays(8),
            'end_time' => now()->subDays(8)->addHour(),
        ]),
        BookingSlot::factory()->create([
            'booking_id' => $pastBooking->id,
            'start_time' => now()->subDays(6),
            'end_time' => now()->subDays(6)->addHour(),
        ]),
    ]);

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $currentBooking->id,
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', $currentSlot))
        ->assertSuccessful();

    $returnedIds = collect($response->json('previousSessions'))->pluck('id')->toArray();

    expect($returnedIds)->toHaveCount(3)->not->toContain($currentSlot->id);

    foreach ($pastSlots as $slot) {
        expect($returnedIds)->toContain($slot->id);
    }
});

test('it respects the limit parameter', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $pastBooking = Booking::factory()->completed()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $currentBooking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    foreach (range(1, 5) as $i) {
        BookingSlot::factory()->create([
            'booking_id' => $pastBooking->id,
            'start_time' => now()->subDays($i + 1),
            'end_time' => now()->subDays($i + 1)->addHour(),
        ]);
    }

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $currentBooking->id,
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', [
            'bookingSlot' => $currentSlot->id,
            'limit' => 2,
        ]))
        ->assertSuccessful();

    expect($response->json('previousSessions'))->toHaveCount(2);
});

test('it only returns sessions before current slot', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $booking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $previousSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->subDays(3),
        'end_time' => now()->subDays(3)->addHour(),
    ]);

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addHours(2),
        'end_time' => now()->addHours(3),
    ]);

    $futureSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDays(3),
        'end_time' => now()->addDays(3)->addHour(),
    ]);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', $currentSlot))
        ->assertSuccessful();

    $returnedIds = collect($response->json('previousSessions'))->pluck('id')->toArray();

    expect($returnedIds)->toContain($previousSlot->id)->not->toContain($currentSlot->id)->not->toContain($futureSlot->id);
});

test('it returns previous booking slots when current slot is first in new booking', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $pastBooking = Booking::factory()->completed()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $currentBooking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $pastSlot = BookingSlot::factory()->create([
        'booking_id' => $pastBooking->id,
        'start_time' => now()->subDays(5),
        'end_time' => now()->subDays(5)->addHour(),
    ]);

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $currentBooking->id,
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', $currentSlot))
        ->assertSuccessful();

    $returnedIds = collect($response->json('previousSessions'))->pluck('id')->toArray();

    expect($returnedIds)->toContain($pastSlot->id);
});

test('it returns empty when member has no previous sessions', function () {
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

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', $currentSlot))
        ->assertSuccessful();

    expect($response->json('previousSessions'))->toBeEmpty();
});

test('it fills results from prior bookings when limit exceeds same-booking slot count', function () {
    $member = User::factory()->create(['role' => Role::Member]);
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $pastBooking = Booking::factory()->completed()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $currentBooking = Booking::factory()->active()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
    ]);

    $pastSlot1 = BookingSlot::factory()->create([
        'booking_id' => $pastBooking->id,
        'start_time' => now()->subDays(10),
        'end_time' => now()->subDays(10)->addHour(),
    ]);

    $pastSlot2 = BookingSlot::factory()->create([
        'booking_id' => $pastBooking->id,
        'start_time' => now()->subDays(8),
        'end_time' => now()->subDays(8)->addHour(),
    ]);

    $priorSlotInCurrentBooking = BookingSlot::factory()->create([
        'booking_id' => $currentBooking->id,
        'start_time' => now()->subDays(2),
        'end_time' => now()->subDays(2)->addHour(),
    ]);

    $currentSlot = BookingSlot::factory()->create([
        'booking_id' => $currentBooking->id,
        'start_time' => now()->addDay(),
        'end_time' => now()->addDay()->addHour(),
    ]);

    $response = actingAsAdmin()
        ->get(route('admin.bookings-slots.circuit-workout-history', [
            'bookingSlot' => $currentSlot->id,
            'limit' => 3,
        ]))
        ->assertSuccessful();

    $returnedIds = collect($response->json('previousSessions'))->pluck('id')->toArray();

    expect($returnedIds)->toHaveCount(3)->toContain($priorSlotInCurrentBooking->id)->toContain($pastSlot1->id)->toContain($pastSlot2->id);
});
