<?php

use App\Actions\Admin\ChangeBookingSlotDateTime;
use App\Enums\Status;
use App\Models\BookingSlot;
use Carbon\Carbon;

test('it sets slot status to Upcoming when start time is in the future', function () {
    $bookingSlot = BookingSlot::factory()->create(['status' => Status::Frozen]);

    $future = Carbon::now()->addDays(3)->setTime(10, 0);

    app(ChangeBookingSlotDateTime::class)->handle($bookingSlot, [
        'start_time' => $future->format('Y-m-d H:i:s'),
        'end_time' => $future->clone()->addHour()->format('Y-m-d H:i:s'),
    ]);

    expect($bookingSlot->fresh()->status)->toBe(Status::Upcoming);
});

test('it sets slot status to Complete when start time is in the past', function () {
    $bookingSlot = BookingSlot::factory()->create(['status' => Status::Upcoming]);

    $past = Carbon::now()->subDays(3)->setTime(10, 0);

    app(ChangeBookingSlotDateTime::class)->handle($bookingSlot, [
        'start_time' => $past->format('Y-m-d H:i:s'),
        'end_time' => $past->clone()->addHour()->format('Y-m-d H:i:s'),
    ]);

    expect($bookingSlot->fresh()->status)->toBe(Status::Complete);
});

test('it updates the booking end_date to the last slot', function () {
    $bookingSlot = BookingSlot::factory()->create();
    $future = Carbon::now()->addDays(5)->setTime(10, 0);

    app(ChangeBookingSlotDateTime::class)->handle($bookingSlot, [
        'start_time' => $future->format('Y-m-d H:i:s'),
        'end_time' => $future->clone()->addHour()->format('Y-m-d H:i:s'),
    ]);

    expect($bookingSlot->fresh()->booking->end_date->toDateString())
        ->toBe($future->toDateString());
});
