<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BookingSlot>
 */
class BookingSlotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'start_time' => Carbon::now()->floorMinutes(30),
            'end_time' => Carbon::now()->addMinutes(60),
            'status' => Status::Upcoming,
            'booking_id' => Booking::factory(),
        ];
    }

    public function forBooking(Booking $booking): BookingSlotFactory
    {
        return $this->state(function () use ($booking) {
            // 1) grab the booking window...
            $startDate = Carbon::parse($booking->start_date)->setTime(7, 0);
            $endDate   = Carbon::parse($booking->end_date)
                ->setTime(21, 0)     // latest end boundary is 21:00
                ->subHour();         // leave room for 1-hour slot

            // 2) pick a random start between 07:00 and (21:00–1h)
            $startDateTime = Carbon::instance(
                $this->faker->dateTimeBetween($startDate, $endDate)
            );

            // 3) floor it to the nearest 30-minute mark
            $startDateTime = $startDateTime->floorMinutes(30);

            // 4) build the one-hour slot
            $endDateTime = (clone $startDateTime)->addHour();

            return [
                'start_time' => $startDateTime,
                'end_time'   => $endDateTime,
                'booking_id' => $booking->id,
                'status'     => $endDateTime->isPast()
                    ? Status::Complete
                    : Status::Upcoming,
            ];
        });
    }

}
