<?php

namespace App\Actions\Admin;

use App\Models\Booking;

class MarkBookingAsPaid
{
    public function handle(Booking $booking): void
    {
        $booking->update(['is_paid' => true]);
    }
}
