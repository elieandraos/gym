<?php

namespace Database\Seeders;

use App\Enums\Category;
use App\Models\Workout;
use Illuminate\Database\Seeder;

class WorkoutSeeder extends Seeder
{
    public function run(): void
    {
        $workouts = [
            Category::Legs->value => [
                'Barbell Squats',
                'Smith Squats',
                'Leg Press',
                'Lunges',
                'Bulgarian Split Squats',
                'Step-Ups',
                'Leg Extensions',
                'Leg Curls',
                'Calf Raises Standing',
                'Hip Thrusts',
                'Glutes Hyperextension',
                'Romanian Deadlifts',
                'Sumo Deadlifts',
                'Single Leg Bridges',
                'Cable Kickbacks',
                'Glute Abductions',
                'Single Leg Reverse Lunges',
            ],
            Category::Biceps->value => [
                'Barbell Curls',
                'Dumbbell Curls',
                'Preacher Curls',
                'Cable Curls',
                'Concentration Curls',
                'Hammer Curls',
                'Chin-Ups',
            ],
            Category::Triceps->value => [
                'Close-Grip Bench Press',
                'Dips',
                'Cable Pushdowns',
                'Overhead Dumbbell Extensions',
                'Skull Crushers',
                'Cable Overhead Rope',
            ],
            Category::Shoulders->value => [
                'Overhead Press Dumbbell',
                'Military Press',
                'Smith Shoulder Press',
                'Arnold Press',
                'Upright Rows',
                'Lateral Raises',
                'Front Raises',
                'Rear Delt Fly',
                'Face Pulls',
            ],
            Category::Chest->value => [
                'Barbell Bench Press',
                'Dumbbell Press',
                'Smith Chest Press',
                'Chest Dips',
                'Cable Fly',
                'Push-Ups',
            ],
            Category::Back->value => [
                'Deadlifts',
                'Pull-Ups',
                'Lat Pulldowns',
                'Barbell Or Dumbbell Rows',
                'Straight Arm Pulldown',
                'Seated Rows',
                'Face Pulls',
                'Kettlebell Rows',
            ],
            Category::Core->value => [
                'Crunches',
                'Sit-Ups',
                'Cable Crunch',
                'Leg Raises',
                'Hanging Knee Raises',
                'Reverse Crunches',
                'Russian Twists',
                'Side Plank',
                'Cable Woodchoppers',
                'Plank Holds',
            ],
        ];

        foreach ($workouts as $category => $names) {
            foreach ($names as $name) {
                Workout::factory()->create([
                    'name' => $name,
                    'category' => $category,
                ]);
            }
        }
    }
}
