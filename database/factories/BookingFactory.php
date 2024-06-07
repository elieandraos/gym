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
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(30),
            'member_id' => User::factory(),
            'trainer_id' => User::factory(),
        ];
    }

    public function active(): BookingFactory
    {
        $startDate = $this->faker->dateTimeBetween('-20 days', '-1 days');
        $endDate = Carbon::instance($startDate)->addDays(30);

        return $this->state(function () use ($startDate, $endDate) {
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    public function completed(int $nbMonths): BookingFactory
    {
        $nbStartDay = 60 * $nbMonths;
        $nbEndDay = 30 * $nbMonths;

        $startDate = $this->faker->dateTimeBetween("-$nbStartDay days", "-$nbEndDay days");
        $endDate = Carbon::instance($startDate)->addDays(30);

        return $this->state(function () use ($startDate, $endDate) {
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }
}
