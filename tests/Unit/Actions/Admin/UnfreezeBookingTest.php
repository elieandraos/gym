<?php

use App\Actions\Admin\UnfreezeBooking;
use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use Carbon\Carbon;

test('it marks the booking as unfrozen and clears frozen_at', function () {
    $booking = Booking::factory()->create(['is_frozen' => true, 'frozen_at' => Carbon::now()]);
    $slot = BookingSlot::factory()->for($booking)->create(['status' => Status::Frozen]);

    $futureTime = Carbon::now()->addDays(3)->setTime(10, 0)->format('Y-m-d H:i:s');
    $futureEndTime = Carbon::now()->addDays(3)->setTime(11, 0)->format('Y-m-d H:i:s');

    (new UnfreezeBooking)->handle($booking, [[
        'id' => $slot->id,
        'start_time' => $futureTime,
        'end_time' => $futureEndTime,
    ]]);

    expect($booking->fresh()->is_frozen)->toBeFalse()
        ->and($booking->fresh()->frozen_at)->toBeNull();
});

test('it sets slot status to Upcoming when start_time is in the future', function () {
    $booking = Booking::factory()->create(['is_frozen' => true, 'frozen_at' => Carbon::now()]);
    $slot = BookingSlot::factory()->for($booking)->create(['status' => Status::Frozen]);

    $futureStart = Carbon::now()->addDays(3)->setTime(10, 0)->format('Y-m-d H:i:s');
    $futureEnd = Carbon::now()->addDays(3)->setTime(11, 0)->format('Y-m-d H:i:s');

    (new UnfreezeBooking)->handle($booking, [[
        'id' => $slot->id,
        'start_time' => $futureStart,
        'end_time' => $futureEnd,
    ]]);

    expect($slot->fresh()->status)->toBe(Status::Upcoming);
});

test('it sets slot status to Complete when start_time is in the past', function () {
    $booking = Booking::factory()->create(['is_frozen' => true, 'frozen_at' => Carbon::now()]);
    $slot = BookingSlot::factory()->for($booking)->create(['status' => Status::Frozen]);

    $pastStart = Carbon::now()->subDays(3)->setTime(10, 0)->format('Y-m-d H:i:s');
    $pastEnd = Carbon::now()->subDays(3)->setTime(11, 0)->format('Y-m-d H:i:s');

    (new UnfreezeBooking)->handle($booking, [[
        'id' => $slot->id,
        'start_time' => $pastStart,
        'end_time' => $pastEnd,
    ]]);

    expect($slot->fresh()->status)->toBe(Status::Complete);
});
