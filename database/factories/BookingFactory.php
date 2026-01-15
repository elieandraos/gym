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
        // Generate random time between 6am and 9pm with 30min intervals
        $possibleTimes = [];
        for ($hour = 6; $hour <= 21; $hour++) {
            $possibleTimes[] = sprintf('%02d:00', $hour);
            if ($hour < 21) {
                $possibleTimes[] = sprintf('%02d:30', $hour);
            }
        }

        $randomTime = $this->faker->randomElement($possibleTimes);
        $formattedTime = Carbon::createFromFormat('H:i', $randomTime)->format('h:i a');

        // Define all possible day patterns
        $dayPatterns = [
            ['Monday', 'Wednesday', 'Friday'],
            ['Tuesday', 'Thursday'],
            ['Monday', 'Thursday'],
            ['Tuesday', 'Thursday', 'Friday'],
            ['Tuesday', 'Thursday', 'Saturday'],
        ];

        $selectedDays = $this->faker->randomElement($dayPatterns);
        $scheduleDays = array_map(fn ($day) => ['day' => $day, 'time' => $formattedTime], $selectedDays);

        return [
            'nb_sessions' => $this->faker->randomElement([8, 12]),
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(30),
            'is_paid' => $this->faker->boolean(80),
            'amount' => 270.00,
            'schedule_days' => $scheduleDays,
            'is_frozen' => false,
            'frozen_at' => null,
            'member_id' => User::factory(),
            'trainer_id' => User::factory(),
        ];
    }

    public function active(): BookingFactory
    {
        $startDate = $this->faker->dateTimeBetween('-20 days', '-5 days');
        $endDate = Carbon::instance($startDate)->addDays(30);

        return $this->state(function () use ($startDate, $endDate) {
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    public function completed(int $nbMonths = 1): BookingFactory
    {
        $nbStartDay = 60 * $nbMonths;
        $nbEndDay = 30 * $nbMonths;

        $startDate = $this->faker->dateTimeBetween("-$nbStartDay days", "-$nbEndDay days");
        $endDate = Carbon::instance($startDate)->addDays(30);

        // Ensure end_date is always in the past (< today) to satisfy history() scope
        if ($endDate->isToday() || $endDate->isFuture()) {
            $endDate = Carbon::yesterday();
        }

        return $this->state(function () use ($startDate, $endDate) {
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];
        });
    }

    public function scheduled(): BookingFactory
    {
        $startDate = $this->faker->dateTimeBetween('5 days', '20 days');
        $endDate = Carbon::instance($startDate)->addDays(30);

        return $this->state(fn () => [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    public function unpaid(): BookingFactory
    {
        return $this->state(fn () => [
            'is_paid' => false,
        ]);
    }
}
