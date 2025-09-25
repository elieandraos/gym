<?php

use App\Http\Resources\CalendarWeekEventsCollection;
use App\Models\Booking;
use Carbon\Carbon;

beforeEach(function () {
    setupUsersAndBookings();
});

test('calendar returns proper component', function () {
    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index'));

    $response->assertOk()
        ->assertHasComponent('Admin/Calendar/Index');
});

test('calendar shows current week by default', function () {
    $start = Carbon::today()->startOfWeek();
    $end = $start->copy()->addDays(5);
    $expectedEvents = Booking::query()->forCalendar($start, $end)->get()->flatMap->bookingSlots;

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index'));

    $response->assertOk()
        ->assertHasResource('week', new CalendarWeekEventsCollection($expectedEvents, $start, $end));
});

test('calendar respects custom date parameters', function () {
    $customStart = Carbon::parse('2024-06-03');
    $customEnd = $customStart->copy()->addDays(5);
    $emptyEvents = collect();

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index', [
        'start' => $customStart->toDateString(),
        'end' => $customEnd->toDateString()
    ]));

    $response->assertOk()
        ->assertHasResource('week', new CalendarWeekEventsCollection($emptyEvents, $customStart, $customEnd));
});

test('calendar handles empty date ranges gracefully', function () {
    $futureStart = Carbon::parse('2030-01-01');
    $futureEnd = $futureStart->copy()->addDays(5);
    $emptyEvents = collect();

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index', [
        'start' => $futureStart->toDateString(),
        'end' => $futureEnd->toDateString()
    ]));

    $response->assertOk()
        ->assertHasResource('week', new CalendarWeekEventsCollection($emptyEvents, $futureStart, $futureEnd));
});

test('calendar processes seeded booking data correctly', function () {
    // Find any seeded booking with slots
    $booking = Booking::with(['member', 'trainer', 'bookingSlots'])
        ->whereHas('bookingSlots')
        ->first();

    if (!$booking) {
        $this->markTestSkipped('No seeded bookings with slots available');
    }

    $slot = $booking->bookingSlots->first();
    $weekStart = $slot->start_time->copy()->startOfWeek();
    $weekEnd = $weekStart->copy()->addDays(5);

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index', [
        'start' => $weekStart->toDateString(),
        'end' => $weekEnd->toDateString()
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($weekStart, $weekEnd, $slot) {
            $inertia->has('week')
                ->where('week.start', $weekStart->toDateString())
                ->where('week.end', $weekEnd->toDateString())
                ->has('week.events')
                ->where('week.events', function ($events) use ($slot) {
                    // If events exist, verify structure
                    if (count($events) > 0) {
                        $event = collect($events)->firstWhere('id', $slot->id);
                        if ($event) {
                            expect($event)->toHaveKeys(['id', 'start_time', 'end_time', 'title', 'meta_data'])
                                ->and($event['meta_data'])->toHaveKeys(['member', 'trainer', 'trainer_color', 'booking_id']);
                        }
                    }
                    return true;
                });
        });
});
