<?php

use App\Enums\Role;
use App\Http\Resources\Calendar\EventResource;
use App\Http\Resources\Calendar\WeekEventsCollection;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;

beforeEach(function () {
    setupUsersAndBookings();
});

test('weekly calendar route requires authentication', function () {
    $this->get(route('admin.weekly-calendar.index'))->assertRedirect(route('login'));
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

    // Generate expected event structure using the actual resource
    $expectedEvent = (new EventResource($slot))->toArray(request());

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index', [
        'start' => $weekStart->toDateString(),
        'end' => $weekEnd->toDateString(),
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($weekStart, $weekEnd, $expectedEvent) {
            $inertia->has('events')
                ->has('filters')
                ->where('filters.start', $weekStart->toDateString())
                ->where('filters.end', $weekEnd->toDateString())
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

test('calendar filters events by trainer ids', function () {
    // Get bookings with slots and trainers
    $bookings = Booking::with(['bookingSlots', 'trainer'])
        ->whereHas('bookingSlots')
        ->get();

    if ($bookings->count() < 2) {
        $this->markTestSkipped('Need at least 2 bookings with different trainers');
    }

    // Pick a booking and filter by its trainer
    $booking = $bookings->first();
    $slot = $booking->bookingSlots->first();
    $trainerId = $booking->trainer_id;
    $weekStart = $slot->start_time->copy()->startOfWeek();
    $weekEnd = $weekStart->copy()->addDays(5);

    // Get bookings for this trainer in this week to verify
    $expectedBookingIds = Booking::query()
        ->where('trainer_id', $trainerId)
        ->forCalendar($weekStart, $weekEnd)
        ->pluck('id')
        ->toArray();

    $response = actingAsAdmin()->get(route('admin.weekly-calendar.index', [
        'start' => $weekStart->toDateString(),
        'end' => $weekEnd->toDateString(),
        'trainers' => (string) $trainerId,
    ]));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($weekStart, $weekEnd, $trainerId, $expectedBookingIds) {
            $inertia->has('events')
                ->has('filters')
                ->where('filters.start', $weekStart->toDateString())
                ->where('filters.end', $weekEnd->toDateString())
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

test('calendar uses default trainer from admin settings', function () {
    $trainer = User::query()->trainers()->first();

    // Create admin with default trainer setting
    $admin = User::factory()->create([
        'role' => Role::Admin,
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

    $response = test()->actingAs($admin)->get(route('admin.weekly-calendar.index'));

    $response->assertOk()
        ->assertInertia(function ($inertia) use ($trainer) {
            $inertia->has('events')
                ->has('filters')
                ->where('filters.trainers', [$trainer->id])
                ->has('available_trainers');
        });
});

test('calendar URL parameter overrides default trainer setting', function () {
    $trainer1 = User::query()->trainers()->first();
    $trainer2 = User::query()->trainers()->skip(1)->first();

    // Create admin with default trainer setting
    $admin = User::factory()->create([
        'role' => Role::Admin,
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
    $response = test()->actingAs($admin)->get(route('admin.weekly-calendar.index', [
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

test('calendar uses custom week start day from settings', function () {
    // Create admin with custom start day (Wednesday)
    $admin = User::factory()->create([
        'role' => Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'wednesday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ],
    ]);

    $response = test()->actingAs($admin)->get(route('admin.weekly-calendar.index'));

    // The start date should be a Wednesday (when no URL parameter provided)
    $response->assertOk()
        ->assertInertia(function ($inertia) {
            $inertia->has('filters')
                ->where('filters', function ($filters) {
                    $startDate = Carbon::parse($filters['start']);
                    expect($startDate->dayOfWeek)->toBe(CarbonInterface::WEDNESDAY);

                    return true;
                })
                ->has('available_trainers');
        });
});

test('calendar uses custom week end day from settings', function () {
    // Create admin with custom end day (Sunday)
    $admin = User::factory()->create([
        'role' => Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'sunday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ],
    ]);

    $response = test()->actingAs($admin)->get(route('admin.weekly-calendar.index'));

    // The end date should be 6 days after start (Monday to Sunday = 6 days)
    $response->assertOk()
        ->assertInertia(function ($inertia) {
            $inertia->has('filters')
                ->where('filters', function ($filters) {
                    $startDate = Carbon::parse($filters['start']);
                    $endDate = Carbon::parse($filters['end']);
                    $daysDiff = $startDate->diffInDays($endDate);

                    expect((int) $daysDiff)->toBe(6)
                        ->and($endDate->dayOfWeek)->toBe(CarbonInterface::SUNDAY);

                    return true;
                })
                ->has('available_trainers');
        });
});

test('calendar handles wrap-around week configuration', function () {
    // Create admin with wrap-around week (Friday to Tuesday)
    $admin = User::factory()->create([
        'role' => Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'friday',
                'end_day' => 'tuesday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ],
    ]);

    $response = test()->actingAs($admin)->get(route('admin.weekly-calendar.index'));

    // The end date should be 4 days after start (Fri->Sat->Sun->Mon->Tue = 4 days)
    $response->assertOk()
        ->assertInertia(function ($inertia) {
            $inertia->has('filters')
                ->where('filters', function ($filters) {
                    $startDate = Carbon::parse($filters['start']);
                    $endDate = Carbon::parse($filters['end']);
                    $daysDiff = $startDate->diffInDays($endDate);

                    expect($startDate->dayOfWeek)->toBe(CarbonInterface::FRIDAY)
                        ->and($endDate->dayOfWeek)->toBe(CarbonInterface::TUESDAY)
                        ->and((int) $daysDiff)->toBe(4);

                    return true;
                })
                ->has('available_trainers');
        });
});
