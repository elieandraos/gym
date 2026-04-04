<?php

use App\Actions\Admin\FreezeBooking;
use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;

test('it marks the booking as frozen with a frozen_at timestamp', function () {
    $booking = Booking::factory()->create(['is_frozen' => false, 'frozen_at' => null]);

    (new FreezeBooking)->handle($booking);

    expect($booking->fresh()->is_frozen)->toBeTrue()
        ->and($booking->fresh()->frozen_at)->not->toBeNull();
});

test('it updates upcoming booking slots to frozen status', function () {
    $booking = Booking::factory()->create(['is_frozen' => false]);
    $upcomingSlot = BookingSlot::factory()->for($booking)->create(['status' => Status::Upcoming]);
    $completeSlot = BookingSlot::factory()->for($booking)->create(['status' => Status::Complete]);

    (new FreezeBooking)->handle($booking);

    expect($upcomingSlot->fresh()->status)->toBe(Status::Frozen)
        ->and($completeSlot->fresh()->status)->toBe(Status::Complete);
});
