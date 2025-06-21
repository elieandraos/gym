<?php

use App\Http\Resources\CalendarWeekCollection;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Services\BookingManager;

beforeEach(function () {
    setupUsersAndBookings();
});

test('owner can view the calendar with expected weekly data', function () {
    ['start' => $spanStart, 'end' => $spanEnd] = BookingManager::getCalendarSpan();

    $bookings = Booking::with([
        'bookingSlots' => fn ($q) => $q->between($spanStart, $spanEnd),
        'member:id,name',
        'trainer:id,name',
    ])
        ->between($spanStart, $spanEnd)
        ->has('bookingSlots')
        ->get();

    actingAsAdmin()
        ->get(route('admin.weekly-calendar.index'))
        ->assertOk()
        ->assertHasComponent('Admin/Calendar/Index')
        ->assertHasResource('weeks', new CalendarWeekCollection($bookings));
});

test('booking outside the 7-week window is not shown', function () {
    ['start' => $spanStart, 'end' => $spanEnd] = BookingManager::getCalendarSpan();

    $booking = Booking::factory()->completed(nbMonths: 6)->create();
    BookingSlot::factory($booking->nb_sessions)
        ->forBooking($booking)
        ->create();

    $expectedBookings = Booking::with([
        'bookingSlots' => fn ($q) => $q->between($spanStart, $spanEnd),
        'member:id,name',
        'trainer:id,name',
    ])
        ->between($spanStart, $spanEnd)
        ->has('bookingSlots')
        ->get();

    actingAsAdmin()
        ->get(route('admin.weekly-calendar.index'))
        ->assertOk()
        ->assertHasComponent('Admin/Calendar/Index')
        ->assertHasResource('weeks', new CalendarWeekCollection($expectedBookings));
});
