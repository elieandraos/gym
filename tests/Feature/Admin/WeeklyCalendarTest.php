<?php

use App\Http\Resources\Calendar\WeekEventsCollection;
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
    $bookings = Booking::query()->forCalendar($start, $end)->get();
    $expectedEvents = $bookings->flatMap->bookingSlots;

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index'));

    $response->assertOk()
        ->assertHasResource('events', new WeekEventsCollection($expectedEvents))
        ->assertInertia(function ($inertia) use ($start, $end) {
            $inertia->has('filters')
                ->where('filters.start', $start->toDateString())
                ->where('filters.end', $end->toDateString())
                ->has('filters.trainers')
                ->where('is_current', $start->isSameWeek(Carbon::today()))
                ->has('available_trainers');
        });
});

test('calendar respects custom date parameters', function () {
    $customStart = Carbon::parse('2024-06-03');
    $customEnd = $customStart->copy()->addDays(5);
    $emptyEvents = collect();

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index', [
        'start' => $customStart->toDateString(),
        'end' => $customEnd->toDateString(),
    ]));

    $response->assertOk()
        ->assertHasResource('events', new WeekEventsCollection($emptyEvents))
        ->assertInertia(function ($inertia) use ($customStart, $customEnd) {
            $inertia->has('filters')
                ->where('filters.start', $customStart->toDateString())
                ->where('filters.end', $customEnd->toDateString())
                ->has('filters.trainers')
                ->where('is_current', $customStart->isSameWeek(Carbon::today()))
                ->has('available_trainers');
        });
});

test('calendar handles empty date ranges gracefully', function () {
    $futureStart = Carbon::parse('2030-01-01');
    $futureEnd = $futureStart->copy()->addDays(5);
    $emptyEvents = collect();

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index', [
        'start' => $futureStart->toDateString(),
        'end' => $futureEnd->toDateString(),
    ]));

    $response->assertOk()
        ->assertHasResource('events', new WeekEventsCollection($emptyEvents))
        ->assertInertia(function ($inertia) use ($futureStart, $futureEnd) {
            $inertia->has('filters')
                ->where('filters.start', $futureStart->toDateString())
                ->where('filters.end', $futureEnd->toDateString())
                ->has('filters.trainers')
                ->where('is_current', $futureStart->isSameWeek(Carbon::today()))
                ->has('available_trainers');
        });
});

test('calendar processes seeded booking data correctly', function () {
    // Find any seeded booking with slots
    $booking = Booking::with(['member', 'trainer', 'bookingSlots'])
        ->whereHas('bookingSlots')
        ->first();

    $slot = $booking->bookingSlots->first();
    $weekStart = $slot->start_time->copy()->startOfWeek();
    $weekEnd = $weekStart->copy()->addDays(5);

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index', [
        'start' => $weekStart->toDateString(),
        'end' => $weekEnd->toDateString(),
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($weekStart, $weekEnd, $slot) {
            $inertia->has('events')
                ->has('filters')
                ->where('filters.start', $weekStart->toDateString())
                ->where('filters.end', $weekEnd->toDateString())
                ->has('available_trainers')
                ->where('events', function ($events) use ($slot) {
                    // If events exist, verify structure
                    if (count($events) > 0) {
                        $event = collect($events)->firstWhere('id', $slot->id);
                        if ($event) {
                            expect($event)->toHaveKeys(['id', 'start_time', 'end_time', 'title', 'url', 'meta_data'])
                                ->and($event['meta_data'])->toHaveKeys(['member', 'trainer', 'trainer_color', 'booking_id', 'short_time']);
                        }
                    }

                    return true;
                });
        });
});
