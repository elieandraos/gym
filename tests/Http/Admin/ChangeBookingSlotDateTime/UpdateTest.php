<?php

use App\Models\Booking;
use App\Models\BookingSlot;
use Carbon\Carbon;

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

test('it provides suggested date and time based on booking schedule', function () {
    $booking = Booking::factory()->create([
        'end_date' => Carbon::parse('2025-07-11'), // Friday
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
    $booking = Booking::factory()->create([
        'end_date' => Carbon::parse('2025-07-14'), // Monday
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
    $booking = Booking::factory()->create([
        'end_date' => Carbon::parse('2025-07-11'),
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
