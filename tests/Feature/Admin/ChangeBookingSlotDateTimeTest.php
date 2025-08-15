<?php

use App\Enums\Status;
use App\Models\BookingSlot;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it loads change date and time edit page', function () {
    $bookingSlot = BookingSlot::query()->first();

    actingAsAdmin()
        ->get(route('admin.change-booking-slot-date-time.edit', $bookingSlot))
        ->assertHasComponent('Admin/ChangeBookingSlotDateTime/Edit')
        ->assertStatus(200);
});

test('it updates booking slot date time and status', function () {
    $bookingSlot = BookingSlot::query()->firstOrFail();

    $newStartTime = now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
    $newEndTime = $newStartTime->copy()->addHour();

    $data = [
        'start_time' => $newStartTime->format('Y-m-d H:i:s'),
        'end_time' => $newEndTime->format('Y-m-d H:i:s'),
    ];

    actingAsAdmin()
        ->put(route('admin.change-booking-slot-date-time.update', $bookingSlot), $data)
        ->assertRedirect(route('admin.bookings-slots.show', $bookingSlot->id));

    $bookingSlot->refresh();

    $expectedStartTime = $newStartTime->clone()->setTimezone('Asia/Beirut');
    $expectedEndTime = $newEndTime->clone()->setTimezone('Asia/Beirut');

    expect($bookingSlot->start_time->toDateTimeString())->toBe($expectedStartTime->toDateTimeString());
    expect($bookingSlot->end_time->toDateTimeString())->toBe($expectedEndTime->toDateTimeString());
    expect($bookingSlot->status)->toBe(Status::Upcoming);
});

test('it sets status to complete when new start time is in the past', function () {
    $bookingSlot = BookingSlot::query()->firstOrFail();

    $newStartTime = now()->subDay()->setHour(10)->setMinute(0)->setSecond(0);
    $newEndTime = $newStartTime->copy()->addHour();

    $data = [
        'start_time' => $newStartTime->format('Y-m-d H:i:s'),
        'end_time' => $newEndTime->format('Y-m-d H:i:s'),
    ];

    actingAsAdmin()
        ->put(route('admin.change-booking-slot-date-time.update', $bookingSlot), $data)
        ->assertRedirect(route('admin.bookings-slots.show', $bookingSlot->id));

    $bookingSlot->refresh();

    expect($bookingSlot->status)->toBe(Status::Complete);
});


test('it validates request data', function () {
    $bookingSlot = BookingSlot::query()->firstOrFail();

    actingAsAdmin()
        ->put(route('admin.change-booking-slot-date-time.update', $bookingSlot), [])
        ->assertSessionHasErrors(['start_time', 'end_time'])
        ->assertStatus(302);
});

test('it validates date format', function () {
    $bookingSlot = BookingSlot::query()->firstOrFail();

    $data = [
        'start_time' => 'invalid',
        'end_time' => 'invalid',
    ];

    actingAsAdmin()
        ->put(route('admin.change-booking-slot-date-time.update', $bookingSlot), $data)
        ->assertSessionHasErrors(['start_time', 'end_time'])
        ->assertStatus(302);
});

