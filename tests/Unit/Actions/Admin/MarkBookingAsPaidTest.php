<?php

use App\Actions\Admin\MarkBookingAsPaid;
use App\Models\Booking;

test('it marks the booking as paid', function () {
    $booking = Booking::factory()->create(['is_paid' => false]);

    app(MarkBookingAsPaid::class)->handle($booking);

    expect($booking->fresh()->is_paid)->toBeTrue();
});
