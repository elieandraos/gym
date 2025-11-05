<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Always seed admin and workouts
        $this->call(AdminSeeder::class);
        $this->call(WorkoutSeeder::class);

        // Only seed test data in non-production environments
        if (! app()->environment('production')) {
            $this->call(TrainerSeeder::class);
            $this->call(MemberSeeder::class);
            $this->call(BookingSeeder::class);
            $this->call(BookingSlotWorkoutSeeder::class);
        }
    }
}
