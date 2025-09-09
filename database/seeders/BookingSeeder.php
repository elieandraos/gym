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

        $members->each(function ($user) use ($trainers) {
            $this->addActiveBooking($user, $trainers);
            $this->addCompletedBookings($user, $trainers, nbMonthsAgo: array_rand([0, 1, 2, 3, 4, 5, 6]));
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
