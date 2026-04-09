<?php

use App\Actions\Admin\CancelBookingSlot;
use App\Enums\Status;
use App\Models\BookingSlot;

test('it sets the booking slot status to Cancelled', function () {
    $bookingSlot = BookingSlot::factory()->create(['status' => Status::Upcoming]);

    app(CancelBookingSlot::class)->handle($bookingSlot);

    expect($bookingSlot->fresh()->status)->toBe(Status::Cancelled);
});
