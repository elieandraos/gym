<?php

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;

describe('active scope', function () {
    it('returns only active bookings', function () {
        Booking::factory()->active()->create();
        Booking::factory()->completed(2)->create();
        Booking::factory()->scheduled()->create();

        $results = Booking::query()->active()->get();

        expect($results)->toHaveCount(1)
            ->and($results->first()->start_date->isPast())->toBeTrue()
            ->and($results->first()->end_date->isFuture())->toBeTrue();
    });

    it('returns no active bookings when none exist', function () {
        Booking::factory()->completed(2)->create();

        $results = Booking::query()->active()->get();

        expect($results)->toHaveCount(0);
    });

    it('includes bookings with start_date and end_date equal to today', function () {
        Booking::factory()->create([
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today(),
        ]);

        $results = Booking::query()->active()->get();

        expect($results)->toHaveCount(1);
    });
});

describe('scheduled scope', function () {
    it('returns only scheduled bookings', function () {
        Booking::factory()->scheduled()->create();
        Booking::factory()->active()->create();
        Booking::factory()->completed(2)->create();

        $results = Booking::query()->scheduled()->get();

        expect($results)->toHaveCount(1)
            ->and($results->first()->start_date->isFuture())->toBeTrue();
    });

    it('returns no scheduled bookings when none exist', function () {
        Booking::factory()->active()->create();
        Booking::factory()->completed(2)->create();

        $results = Booking::query()->scheduled()->get();

        expect($results)->toHaveCount(0);
    });

    it('does not include bookings with start_date equal to today', function () {
        Booking::factory()->create([
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(3),
        ]);

        $results = Booking::query()->scheduled()->get();

        expect($results)->toHaveCount(0);
    });

    it('does not include bookings with end_date equal to today', function () {
        Booking::factory()->create([
            'start_date' => Carbon::today()->subDays(5),
            'end_date' => Carbon::today(),
        ]);

        $results = Booking::query()->history()->get();

        expect($results)->toHaveCount(0);
    });
});

describe('history scope', function () {
    it('returns only completed bookings', function () {
        Booking::factory()->completed(2)->create();
        Booking::factory()->active()->create();
        Booking::factory()->scheduled()->create();

        $results = Booking::query()->history()->get();

        expect($results)->toHaveCount(1)
            ->and($results->first()->end_date->isPast())->toBeTrue();
    });

    it('returns no completed bookings when none exist', function () {
        Booking::factory()->active()->create();
        Booking::factory()->scheduled()->create();

        $results = Booking::query()->history()->get();

        expect($results)->toHaveCount(0);
    });
});

describe('forCalendar scope', function () {
    it('excludes frozen booking slots from calendar', function () {
        $member = User::factory()->create();
        $trainer = User::factory()->create();

        $booking = Booking::factory()->active()->create([
            'member_id' => $member->id,
            'trainer_id' => $trainer->id,
        ]);

        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        // Create upcoming slot within the week range
        BookingSlot::factory()->create([
            'booking_id' => $booking->id,
            'status' => Status::Upcoming,
            'start_time' => $start->copy()->addDays(1)->setHour(10),
            'end_time' => $start->copy()->addDays(1)->setHour(11),
        ]);

        // Create frozen slot within the week range
        BookingSlot::factory()->create([
            'booking_id' => $booking->id,
            'status' => Status::Frozen,
            'start_time' => $start->copy()->addDays(2)->setHour(10),
            'end_time' => $start->copy()->addDays(2)->setHour(11),
        ]);

        // Create cancelled slot within the week range
        BookingSlot::factory()->create([
            'booking_id' => $booking->id,
            'status' => Status::Cancelled,
            'start_time' => $start->copy()->addDays(3)->setHour(10),
            'end_time' => $start->copy()->addDays(3)->setHour(11),
        ]);

        $results = Booking::query()->forCalendar($start, $end)->get();

        expect($results)->toHaveCount(1)
            ->and($results->first()->bookingSlots)->toHaveCount(1)
            ->and($results->first()->bookingSlots->first()->status)->toBe(Status::Upcoming);
    });
});
