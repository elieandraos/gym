<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'nb_sessions' => $this->faker->randomElement([8, 12]),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(30),
            'member_id' => User::factory(),
            'trainer_id' => User::factory(),
        ];
    }
}
