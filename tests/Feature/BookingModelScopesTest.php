<?php

use App\Models\Booking;
use Carbon\Carbon;

describe('active scope', function () {
    it('returns only active bookings', function () {
        Booking::factory()->active()->create();
        Booking::factory()->completed(2)->create();
        Booking::factory()->scheduled()->create();

        $results = Booking::query()->active()->get();

        expect($results)->toHaveCount(1);
        expect($results->first()->start_date->isPast())->toBeTrue();
        expect($results->first()->end_date->isFuture())->toBeTrue();
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

        expect($results)->toHaveCount(1);
        expect($results->first()->start_date->isFuture())->toBeTrue();
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

        expect($results)->toHaveCount(1);
        expect($results->first()->end_date->isPast())->toBeTrue();
    });

    it('returns no completed bookings when none exist', function () {
        Booking::factory()->active()->create();
        Booking::factory()->scheduled()->create();

        $results = Booking::query()->history()->get();

        expect($results)->toHaveCount(0);
    });
});
