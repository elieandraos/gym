<?php

namespace App\Actions\Admin;

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateBooking
{
    public function handle(array $attributes): Booking
    {
        return DB::transaction(function () use ($attributes) {
            $bookingSlots = [];

            foreach ($attributes['booking_slots_dates'] as $date) {
                $startDate = Carbon::parse($date);
                $bookingSlots[] = new BookingSlot([
                    'start_time' => $startDate,
                    'end_time' => $startDate->clone()->addHour(),
                    'status' => $startDate < Carbon::now() ? Status::Complete->value : Status::Upcoming->value,
                ]);
            }

            $booking = Booking::query()->create(
                collect($attributes)->except(['booking_slots_dates'])->all()
            );

            $booking->bookingSlots()->saveMany($bookingSlots);
            $booking->updateEndDateToLastSlot();

            return $booking;
        });
    }
}
