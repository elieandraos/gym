<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $members = User::factory(10)->create([
            'role' => Role::Member,
        ]);

        $trainers = User::factory(3)->create([
            'role' => Role::Trainer,
        ]);

        $members->each(function ($user) use ($trainers) {
            $booking = Booking::factory()->create([
                'member_id' => $user->id,
                'trainer_id' => $trainers->random()->id,
            ]);

            BookingSlot::factory($booking->nb_sessions)
                ->forBooking($booking)
                ->create([
                    'booking_id' => $booking->id,
                ]);
        });

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'test@example.com',
            'role' => 'Admin',
        ]);
    }
}
