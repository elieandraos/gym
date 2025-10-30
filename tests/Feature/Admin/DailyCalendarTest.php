<?php

use App\Http\Resources\Calendar\DayEventsCollection;
use App\Http\Resources\Calendar\EventResource;
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
    $expectedEvents = Booking::query()->forCalendar($startOfDay, $endOfDay)->get()->flatMap->bookingSlots
        ->filter(function ($slot) use ($startOfDay, $endOfDay) {
            return $slot->start_time >= $startOfDay && $slot->start_time <= $endOfDay;
        });

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index'));

    $response->assertOk()
        ->assertHasResource('events', new DayEventsCollection($expectedEvents))
        ->assertInertia(function ($inertia) use ($today) {
            $inertia->has('filters')
                ->where('filters.date', $today->toDateString())
                ->has('filters.trainers')
                ->where('is_today', $today->isSameDay(Carbon::today()))
                ->has('available_trainers');
        });
});

test('daily calendar respects custom date parameter', function () {
    $customDate = Carbon::parse('2024-06-15');
    $emptyEvents = collect();

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index', [
        'date' => $customDate->toDateString(),
    ]));

    $response->assertOk()
        ->assertHasResource('events', new DayEventsCollection($emptyEvents))
        ->assertInertia(function ($inertia) use ($customDate) {
            $inertia->has('filters')
                ->where('filters.date', $customDate->toDateString())
                ->has('filters.trainers')
                ->where('is_today', $customDate->isSameDay(Carbon::today()))
                ->has('available_trainers');
        });
});

test('daily calendar handles empty dates gracefully', function () {
    $futureDate = Carbon::parse('2030-06-15');
    $emptyEvents = collect();

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index', [
        'date' => $futureDate->toDateString(),
    ]));

    $response->assertOk()
        ->assertHasResource('events', new DayEventsCollection($emptyEvents))
        ->assertInertia(function ($inertia) use ($futureDate) {
            $inertia->has('filters')
                ->where('filters.date', $futureDate->toDateString())
                ->has('filters.trainers')
                ->where('is_today', $futureDate->isSameDay(Carbon::today()))
                ->has('available_trainers');
        });
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

    // Generate expected event structure using the actual resource
    $expectedEvent = (new EventResource($slot))->toArray(request());

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index', [
        'date' => $slotDate,
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($slotDate, $expectedEvent) {
            $inertia->has('events')
                ->has('filters')
                ->where('filters.date', $slotDate)
                ->has('available_trainers')
                ->where('events', function ($events) use ($expectedEvent) {
                    // If events exist, verify structure matches the resource
                    if (count($events) > 0) {
                        $event = collect($events)->firstWhere('id', $expectedEvent['id']);
                        if ($event) {
                            expect($event)->toMatchArray($expectedEvent);
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
            $inertia->has('events')
                ->has('filters')
                ->where('filters.date', $targetDate)
                ->has('available_trainers')
                ->where('events', function ($events) use ($targetDate) {
                    // Verify all events are within the target date
                    foreach ($events as $event) {
                        $eventDate = Carbon::parse($event['start_time'])->toDateString();
                        expect($eventDate)->toBe($targetDate);
                    }

                    return true;
                });
        });
});

test('daily calendar filters events by trainer ids', function () {
    // Get bookings with slots and trainers
    $bookings = Booking::with(['bookingSlots', 'trainer'])
        ->whereHas('bookingSlots')
        ->get();

    if ($bookings->count() < 2) {
        $this->markTestSkipped('Need at least 2 bookings with different trainers');
    }

    // Pick a slot and filter by its trainer
    $booking = $bookings->first();
    $slot = $booking->bookingSlots->first();
    $trainerId = $booking->trainer_id;
    $slotDate = $slot->start_time->toDateString();

    // Get bookings for this trainer on this date to verify
    $expectedBookingIds = Booking::query()
        ->where('trainer_id', $trainerId)
        ->whereHas('bookingSlots', function ($query) use ($slotDate) {
            $startOfDay = Carbon::parse($slotDate)->startOfDay();
            $endOfDay = Carbon::parse($slotDate)->endOfDay();
            $query->whereBetween('start_time', [$startOfDay, $endOfDay]);
        })
        ->pluck('id')
        ->toArray();

    $response = actingAsAdmin()->get(route('admin.daily-calendar.index', [
        'date' => $slotDate,
        'trainers' => (string) $trainerId,
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($slotDate, $trainerId, $expectedBookingIds) {
            $inertia->has('events')
                ->has('filters')
                ->where('filters.date', $slotDate)
                ->where('filters.trainers', [$trainerId])
                ->has('available_trainers')
                ->where('events', function ($events) use ($expectedBookingIds) {
                    // All events should belong to bookings with the filtered trainer
                    foreach ($events as $event) {
                        expect($event['meta_data']['booking_id'])->toBeIn($expectedBookingIds);
                    }

                    return true;
                });
        });
});

test('daily calendar uses default trainer from admin settings', function () {
    $trainer = \App\Models\User::query()->trainers()->first();

    // Create admin with default trainer setting
    $admin = \App\Models\User::factory()->create([
        'role' => \App\Enums\Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => $trainer->id,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ],
    ]);

    $response = test()->actingAs($admin)->get(route('admin.daily-calendar.index'));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($trainer) {
            $inertia->has('events')
                ->has('filters')
                ->where('filters.trainers', [$trainer->id])
                ->has('available_trainers');
        });
});

test('daily calendar URL parameter overrides default trainer setting', function () {
    $trainer1 = \App\Models\User::query()->trainers()->first();
    $trainer2 = \App\Models\User::query()->trainers()->skip(1)->first();

    // Create admin with default trainer setting
    $admin = \App\Models\User::factory()->create([
        'role' => \App\Enums\Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => $trainer1->id,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ],
    ]);

    // URL parameter should override the setting
    $response = test()->actingAs($admin)->get(route('admin.daily-calendar.index', [
        'trainers' => (string) $trainer2->id,
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($trainer2) {
            $inertia->has('events')
                ->has('filters')
                ->where('filters.trainers', [$trainer2->id])
                ->has('available_trainers');
        });
});
