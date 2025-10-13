<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use App\Services\BookingManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = User::query()->trainers()->get();
        $members = User::query()->members()->get();

        $members->each(function ($user, $index) use ($trainers) {
            // Add completed bookings (history) - at least 2 months ago to avoid overlap
            $this->addCompletedBookings($user, $trainers, nbMonthsAgo: fake()->numberBetween(2, 6));

            // First 3 members get soon-to-expire booking only (for testing renewals)
            if ($index < 3) {
                $this->addSoonToExpireBooking($user, $trainers);
            } else {
                // Other members get regular active booking
                $this->addActiveBooking($user, $trainers);
            }
        });
    }

    protected function addActiveBooking(User $member, Collection $trainers): void
    {
        /** @var User $randomTrainer */
        $randomTrainer = $trainers->random();

        $factory = Booking::factory()->active();

        // Make some active bookings unpaid (approximately 20%)
        if (fake()->boolean(20)) {
            $factory = $factory->unpaid();
        }

        /** @var Booking $booking */
        $booking = $factory->create([
            'member_id' => $member->id,
            'trainer_id' => $randomTrainer->id,
        ]);

        $this->createBookingSlotsForBooking($booking);
    }

    protected function addCompletedBookings(User $member, Collection $trainers, int $nbMonthsAgo = 3): void
    {
        if ($nbMonthsAgo <= 0) {
            return;
        }

        /** @var User $randomTrainer */
        $randomTrainer = $trainers->random();

        /** @var Booking $booking */
        $booking = Booking::factory()
            ->completed($nbMonthsAgo)
            ->create([
                'member_id' => $member->id,
                'trainer_id' => $randomTrainer->id,
            ]);

        $this->createBookingSlotsForBooking($booking);
    }

    protected function addSoonToExpireBooking(User $member, Collection $trainers): void
    {
        /** @var User $randomTrainer */
        $randomTrainer = $trainers->random();

        /** @var Booking $booking */
        $booking = Booking::factory()->active()->create([
            'member_id' => $member->id,
            'trainer_id' => $randomTrainer->id,
            'nb_sessions' => 12,
        ]);

        // Generate proper slot dates using BookingManager based on schedule_days
        $slotDates = BookingManager::generateRepeatableDates(
            Carbon::parse($booking->start_date),
            $booking->nb_sessions,
            $booking->schedule_days
        );

        // Create 12 slots: first 10 completed, last 2 upcoming
        foreach ($slotDates as $index => $slotDate) {
            $startTime = Carbon::parse($slotDate);
            $endTime = $startTime->copy()->addHour();

            BookingSlot::query()->create([
                'booking_id' => $booking->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $index < 10 ? Status::Complete : Status::Upcoming,
            ]);
        }

        // Update booking end_date to the last slot's date
        $lastSlotDate = Carbon::parse(end($slotDates));
        $booking->update([
            'end_date' => $lastSlotDate->toDateString(),
        ]);
    }

    protected function createBookingSlotsForBooking(Booking $booking): void
    {
        // Generate proper slot dates using BookingManager based on schedule_days
        $slotDates = BookingManager::generateRepeatableDates(
            Carbon::parse($booking->start_date),
            $booking->nb_sessions,
            $booking->schedule_days
        );

        // Create slots with proper dates and times from schedule
        foreach ($slotDates as $slotDate) {
            $startTime = Carbon::parse($slotDate);
            $endTime = $startTime->copy()->addHour();

            BookingSlot::query()->create([
                'booking_id' => $booking->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $startTime->isPast() ? Status::Complete : Status::Upcoming,
            ]);
        }

        $booking->updateEndDateToLastSlot();
    }
}
