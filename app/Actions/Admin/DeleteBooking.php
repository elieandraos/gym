<?php

namespace App\Actions\Admin;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class DeleteBooking
{
    public function handle(Booking $booking): void
    {
        DB::transaction(function () use ($booking) {
            $booking->delete();
        });
    }
}
