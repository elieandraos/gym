<?php

namespace App\Actions\Admin;

use App\Enums\Status;
use App\Models\BookingSlot;

class CancelBookingSlot
{
    public function handle(BookingSlot $bookingSlot): void
    {
        $bookingSlot->update(['status' => Status::Cancelled]);
    }
}
