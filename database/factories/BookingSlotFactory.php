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
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addMinutes(60),
            'status' => Status::Upcoming,
            'booking_id' => Booking::factory(),
        ];
    }

    public function forBooking(Booking $booking): BookingSlotFactory
    {
        return $this->state(function () use ($booking) {
            $startDate = $booking->start_date;
            $endDate = $booking->end_date;

            // Generate a random start time within the booking date range
            $startDateTime = $this->faker->dateTimeBetween($startDate, $endDate);

            // Ensure the start time is at least one hour before the end date
            if ($startDateTime->format('Y-m-d H:i:s') > $endDate->subHour()->format('Y-m-d H:i:s')) {
                $startDateTime = $endDate->subHour();  // Adjust start time back by one hour if too close to end date
            }

            // Clone the startDateTime and add one hour for endDateTime
            $endDateTime = (clone $startDateTime)->modify('+1 hour');

            return [
                'start_time' => $startDateTime,
                'end_time' => $endDateTime,
                'booking_id' => $booking->id,
                'status' => $endDateTime < Carbon::now() ? Status::Complete : Status::Upcoming,
            ];
        });
    }
}
