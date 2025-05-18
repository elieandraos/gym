<?php

use App\Models\Booking;

it('returns only active bookings', function () {
    Booking::factory()->active()->create();
    Booking::factory()->completed(2)->create();
    Booking::factory()->scheduled()->create();

    $results = Booking::query()->active()->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->start_date->isPast())->toBeTrue();
    expect($results->first()->end_date->isFuture())->toBeTrue();
});

it('returns only scheduled bookings', function () {
    Booking::factory()->scheduled()->create();
    Booking::factory()->active()->create();
    Booking::factory()->completed(2)->create();

    $results = Booking::query()->scheduled()->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->start_date->isFuture())->toBeTrue();
});

it('returns only completed bookings', function () {
    Booking::factory()->completed(2)->create();
    Booking::factory()->active()->create();
    Booking::factory()->scheduled()->create();

    $results = Booking::query()->history()->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->end_date->isPast())->toBeTrue();
});

it('returns no active bookings when none exist', function () {
    Booking::factory()->completed(2)->create();

    $results = Booking::query()->active()->get();

    expect($results)->toHaveCount(0);
});

it('returns no scheduled bookings when none exist', function () {
    Booking::factory()->active()->create();
    Booking::factory()->completed(2)->create();

    $results = Booking::query()->scheduled()->get();

    expect($results)->toHaveCount(0);
});

it('returns no completed bookings when none exist', function () {
    Booking::factory()->active()->create();
    Booking::factory()->scheduled()->create();

    $results = Booking::query()->history()->get();

    expect($results)->toHaveCount(0);
});
