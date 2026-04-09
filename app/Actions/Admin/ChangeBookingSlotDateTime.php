<?php

namespace App\Actions\Admin;

use App\Enums\Status;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChangeBookingSlotDateTime
{
    public function handle(BookingSlot $bookingSlot, array $attributes): void
    {
        DB::transaction(function () use ($bookingSlot, $attributes) {
            $start = Carbon::createFromFormat('Y-m-d H:i:s', $attributes['start_time'], 'Asia/Beirut');
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $attributes['end_time'], 'Asia/Beirut');

            $bookingSlot->update([
                'start_time' => $start,
                'end_time' => $end,
                'status' => $start->isPast() ? Status::Complete : Status::Upcoming,
            ]);

            $bookingSlot->booking->updateEndDateToLastSlot();
        });
    }
}
