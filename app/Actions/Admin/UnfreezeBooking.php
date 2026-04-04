<?php

namespace App\Actions\Admin;

use App\Enums\Status;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UnfreezeBooking
{
    public function handle(Booking $booking, array $slots): void
    {
        DB::transaction(function () use ($booking, $slots) {
            foreach ($slots as $slotData) {
                $bookingSlot = $booking->bookingSlots()->findOrFail($slotData['id']);

                $start = Carbon::createFromFormat('Y-m-d H:i:s', $slotData['start_time'], 'Asia/Beirut');
                $end = Carbon::createFromFormat('Y-m-d H:i:s', $slotData['end_time'], 'Asia/Beirut');

                $bookingSlot->update([
                    'start_time' => $start,
                    'end_time' => $end,
                    'status' => $start->isPast() ? Status::Complete : Status::Upcoming,
                ]);
            }

            $booking->update([
                'is_frozen' => false,
                'frozen_at' => null,
            ]);

            $booking->updateEndDateToLastSlot();
        });
    }
}
