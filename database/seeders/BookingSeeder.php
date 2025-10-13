<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = User::query()->trainers()->get();
        $members = User::query()->members()->get();

        $members->each(function ($user, $index) use ($trainers) {
            $this->addActiveBooking($user, $trainers);
            $this->addCompletedBookings($user, $trainers, nbMonthsAgo: array_rand([0, 1, 2, 3, 4, 5, 6]));

            // Add 2-3 expiring bookings (with only 2 remaining sessions)
            if ($index < 3) {
                $this->addExpiringBooking($user, $trainers);
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

    protected function addExpiringBooking(User $member, Collection $trainers): void
    {
        /** @var User $randomTrainer */
        $randomTrainer = $trainers->random();

        /** @var Booking $booking */
        $booking = Booking::factory()->active()->create([
            'member_id' => $member->id,
            'trainer_id' => $randomTrainer->id,
            'nb_sessions' => 12,
        ]);

        // Create slots where 10 are completed and 2 are upcoming
        $completedSlots = BookingSlot::factory(10)
            ->forBooking($booking)
            ->create()
            ->each(function ($slot) {
                $slot->update(['status' => \App\Enums\Status::Complete]);
            });

        $upcomingSlots = BookingSlot::factory(2)
            ->forBooking($booking)
            ->create();

        $allSlots = $completedSlots->merge($upcomingSlots);
        $lastBookingSlot = $allSlots->sortByDesc('start_time')->first();

        $booking->update([
            'end_date' => $lastBookingSlot->start_time->toDateString(),
        ]);
    }

    protected function createBookingSlotsForBooking(Booking $booking): void
    {
        $bookingSlots = BookingSlot::factory($booking->nb_sessions)
            ->forBooking($booking)
            ->create();

        $lastBookingSlot = $bookingSlots->sortByDesc('start_time')->first();

        $booking->update([
            'end_date' => $lastBookingSlot->start_time->toDateString(),
        ]);
    }
}
