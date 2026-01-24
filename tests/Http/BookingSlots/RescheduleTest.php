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
        ->assertRedirect(route('admin.bookings-slots.show', [
            'bookingSlot' => $bookingSlot->id,
            'booking_id' => $bookingSlot->booking_id,
        ]));

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
        ->assertRedirect(route('admin.bookings-slots.show', [
            'bookingSlot' => $bookingSlot->id,
            'booking_id' => $bookingSlot->booking_id,
        ]));

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

test('it updates booking end_date when changing the last slot', function () {
    setupUsersAndBookings();

    $booking = \App\Models\Booking::factory()->active()->create();

    // Create 3 slots
    $slot1 = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDays(5),
        'end_time' => now()->addDays(5)->addHour(),
    ]);

    $slot2 = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDays(10),
        'end_time' => now()->addDays(10)->addHour(),
    ]);

    $slot3 = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
        'start_time' => now()->addDays(15), // This is the last slot
        'end_time' => now()->addDays(15)->addHour(),
    ]);

    // Set initial booking end_date to last slot
    $booking->updateEndDateToLastSlot();
    expect($booking->fresh()->end_date->toDateString())->toBe(now()->addDays(15)->toDateString());

    // Change the last slot to a new date
    $newStartTime = now()->addDays(20)->setHour(10)->setMinute(0)->setSecond(0);
    $newEndTime = $newStartTime->copy()->addHour();

    $data = [
        'start_time' => $newStartTime->format('Y-m-d H:i:s'),
        'end_time' => $newEndTime->format('Y-m-d H:i:s'),
    ];

    actingAsAdmin()
        ->put(route('admin.change-booking-slot-date-time.update', $slot3), $data);

    // Booking end_date should now be updated to the new last slot date
    expect($booking->fresh()->end_date->toDateString())->toBe(now()->addDays(20)->toDateString());
});

test('it provides suggested date and time based on booking schedule', function () {
    $booking = \App\Models\Booking::factory()->create([
        'end_date' => \Carbon\Carbon::parse('2025-07-11'), // Friday
        'schedule_days' => [
            ['day' => 'Monday', 'time' => '10:00 AM'],
            ['day' => 'Wednesday', 'time' => '02:00 PM'],
            ['day' => 'Friday', 'time' => '03:00 PM'],
        ],
    ]);

    $bookingSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
    ]);

    actingAsAdmin()
        ->get(route('admin.change-booking-slot-date-time.edit', $bookingSlot))
        ->assertHasComponent('Admin/ChangeBookingSlotDateTime/Edit')
        ->assertHasProp('suggestedDate', '2025-07-14') // Next Monday
        ->assertHasProp('suggestedTime', '10:00 AM') // Monday's time
        ->assertStatus(200);
});

test('it provides correct time for different days in schedule', function () {
    $booking = \App\Models\Booking::factory()->create([
        'end_date' => \Carbon\Carbon::parse('2025-07-14'), // Monday
        'schedule_days' => [
            ['day' => 'Monday', 'time' => '09:00 AM'],
            ['day' => 'Wednesday', 'time' => '02:30 PM'],
            ['day' => 'Friday', 'time' => '04:00 PM'],
        ],
    ]);

    $bookingSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
    ]);

    actingAsAdmin()
        ->get(route('admin.change-booking-slot-date-time.edit', $bookingSlot))
        ->assertHasComponent('Admin/ChangeBookingSlotDateTime/Edit')
        ->assertHasProp('suggestedDate', '2025-07-16') // Next Wednesday
        ->assertHasProp('suggestedTime', '02:30 PM') // Wednesday's time
        ->assertStatus(200);
});

test('it handles booking with no schedule days gracefully', function () {
    $booking = \App\Models\Booking::factory()->create([
        'end_date' => \Carbon\Carbon::parse('2025-07-11'),
        'schedule_days' => null,
    ]);

    $bookingSlot = BookingSlot::factory()->create([
        'booking_id' => $booking->id,
    ]);

    actingAsAdmin()
        ->get(route('admin.change-booking-slot-date-time.edit', $bookingSlot))
        ->assertHasComponent('Admin/ChangeBookingSlotDateTime/Edit')
        ->assertHasProp('suggestedDate', null)
        ->assertHasProp('suggestedTime', null)
        ->assertStatus(200);
});
