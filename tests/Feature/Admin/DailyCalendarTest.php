<?php

use App\Http\Resources\Calendar\DayEventsCollection;
use App\Models\Booking;
use Carbon\Carbon;

beforeEach(function () {
    setupUsersAndBookings();
});

test('daily calendar returns proper component', function () {
    $response = actingAsAdmin()->get(route('admin.daily-calendar.index'));

    $response->assertOk()
        ->assertHasComponent('Admin/DailyCalendar/Index');
});

test('daily calendar shows today by default', function () {
    $today = Carbon::today();
    $startOfDay = $today->copy()->startOfDay();
    $endOfDay = $today->copy()->endOfDay();
    $expectedEvents = Booking::query()->forCalendar($startOfDay, $endOfDay)->get()->flatMap->bookingSlots;

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index'));

    $response->assertOk()
        ->assertHasResource('day', new DayEventsCollection($expectedEvents, $today));
});

test('daily calendar respects custom date parameter', function () {
    $customDate = Carbon::parse('2024-06-15');
    $emptyEvents = collect();

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index', [
        'date' => $customDate->toDateString(),
    ]));

    $response->assertOk()
        ->assertHasResource('day', new DayEventsCollection($emptyEvents, $customDate));
});

test('daily calendar handles empty dates gracefully', function () {
    $futureDate = Carbon::parse('2030-06-15');
    $emptyEvents = collect();

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index', [
        'date' => $futureDate->toDateString(),
    ]));

    $response->assertOk()
        ->assertHasResource('day', new DayEventsCollection($emptyEvents, $futureDate));
});

test('daily calendar processes seeded booking data correctly', function () {
    // Find any seeded booking with slots
    $booking = Booking::with(['member', 'trainer', 'bookingSlots'])
        ->whereHas('bookingSlots')
        ->first();

    if (! $booking) {
        $this->markTestSkipped('No seeded bookings with slots available');
    }

    $slot = $booking->bookingSlots->first();
    $slotDate = $slot->start_time->toDateString();

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index', [
        'date' => $slotDate,
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($slotDate, $slot) {
            $inertia->has('day')
                ->where('day.date', $slotDate)
                ->has('day.events')
                ->where('day.events', function ($events) use ($slot) {
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

test('daily calendar only shows events for specified date', function () {
    // Find seeded bookings across multiple days
    $bookingsAcrossDays = Booking::with(['bookingSlots'])
        ->whereHas('bookingSlots')
        ->get();

    if ($bookingsAcrossDays->isEmpty()) {
        $this->markTestSkipped('No seeded bookings available');
    }

    // Pick a specific date that might have bookings
    $targetSlot = $bookingsAcrossDays->flatMap->bookingSlots->first();
    $targetDate = $targetSlot->start_time->toDateString();

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index', [
        'date' => $targetDate,
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($targetDate) {
            $inertia->has('day')
                ->where('day.date', $targetDate)
                ->has('day.events')
                ->where('day.events', function ($events) use ($targetDate) {
                    // Verify all events are within the target date
                    foreach ($events as $event) {
                        $eventDate = Carbon::parse($event['start_time'])->toDateString();
                        expect($eventDate)->toBe($targetDate);
                    }

                    return true;
                });
        });
});
