<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Models\BookingSlot;
use App\Models\BookingSlotWorkout;
use App\Models\BookingSlotWorkoutSet;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class BookingSlotWorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $pastBookingSlots = BookingSlot::query()->where('end_time', '<', now())->get();

        foreach ($pastBookingSlots as $bookingSlot) {
            $this->seedWorkoutsForBookingSlot($bookingSlot);
        }
    }

    private function seedWorkoutsForBookingSlot(BookingSlot $bookingSlot): void
    {
        $categories = $this->selectRandomCategories();
        $selectedWorkouts = $this->getWorkoutsByCategories($categories);

        foreach ($selectedWorkouts as $workout) {
            $bookingSlotWorkout = BookingSlotWorkout::query()->create([
                'booking_slot_id' => $bookingSlot->id,
                'workout_id' => $workout->id,
            ]);

            $this->createSetsForWorkout($bookingSlotWorkout);
        }
    }

    private function selectRandomCategories(): array
    {
        $allCategories = Category::cases();
        $groupCount = fake()->numberBetween(2, 3);

        return fake()->randomElements($allCategories, $groupCount);
    }

    private function getWorkoutsByCategories(array $categories): array
    {
        $selectedWorkouts = [];

        foreach ($categories as $category) {
            $workouts = Workout::query()
                ->where('category', $category)
                ->inRandomOrder()
                ->limit(3)
                ->get();

            $selectedWorkouts = array_merge($selectedWorkouts, $workouts->all());
        }

        return $selectedWorkouts;
    }

    private function createSetsForWorkout(BookingSlotWorkout $bookingSlotWorkout): void
    {
        $faker = fake();
        $setsCount = 3;
        $isWeighted = $faker->boolean(70);
        $isTimed = ! $isWeighted;

        for ($i = 0; $i < $setsCount; $i++) {
            BookingSlotWorkoutSet::query()->create([
                'booking_slot_workout_id' => $bookingSlotWorkout->id,
                'reps' => $isWeighted ? 12 : 1,
                'weight_in_kg' => $isWeighted ? $this->generateWeight() : null,
                'duration_in_seconds' => $isTimed ? $faker->numberBetween(30, 180) : null,
            ]);
        }
    }

    private function generateWeight(): float
    {
        $faker = fake();
        $increments = [1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5];
        $multiplier = $faker->numberBetween(1, 7);
        $baseWeight = $faker->randomElement($increments);

        return min(35, $baseWeight * $multiplier);
    }
}
