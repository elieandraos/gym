<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = User::factory(3)->create([
            'role' => Role::Trainer->value,
        ]);

        $members = User::factory(50)->create([
            'role' => Role::Member->value,
        ]);

        $members->each(function ($user) use ($trainers) {
            $this->addActiveBooking($user, $trainers);
            $this->addPreviousBookings($user, $trainers, array_rand([0, 1, 2, 3, 4, 5, 6]));
        });

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'test@example.com',
            'role' => Role::Admin->value,
        ]);
    }

    protected function addActiveBooking(User $user, Collection $trainers): void
    {
        $booking = Booking::factory()
            ->active()
            ->create([
                'member_id' => $user->id,
                'trainer_id' => $trainers->random()->id,
            ]);

        $bookingSlots = BookingSlot::factory($booking->nb_sessions)
            ->forBooking($booking)
            ->create();

        $lastBookingSlot = $bookingSlots->sortByDesc('start_time')->first();

        $booking->update([
            'end_date' => $lastBookingSlot->start_time->toDateString(),
        ]);
    }

    protected function addPreviousBookings(User $user, Collection $trainers, int $nbMonthsAgo): void
    {
        if ($nbMonthsAgo <= 0) {
            return;
        }

        $booking = Booking::factory()
            ->completed($nbMonthsAgo)
            ->create([
                'member_id' => $user->id,
                'trainer_id' => $trainers->random()->id,
            ]);

        $bookingSlots = BookingSlot::factory($booking->nb_sessions)
            ->forBooking($booking)
            ->create();

        $lastBookingSlot = $bookingSlots->sortByDesc('start_time')->first();

        $booking->update([
            'end_date' => $lastBookingSlot->start_time->toDateString(),
        ]);
    }
}
