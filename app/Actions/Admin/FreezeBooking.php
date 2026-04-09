<?php

namespace App\Actions\Admin;

use App\Enums\Status;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FreezeBooking
{
    public function handle(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking->update([
                'is_frozen' => true,
                'frozen_at' => Carbon::now(),
            ]);

            $booking->bookingSlots()
                ->where('status', Status::Upcoming)
                ->update(['status' => Status::Frozen]);
        });
    }
}
