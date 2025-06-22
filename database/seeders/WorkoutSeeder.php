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
            Category::Chest->value => [
                'Barbell Bench Press',
                'Dumbbell Flies',
                'Push Ups',
                'Incline Bench Press',
                'Chest Dips',
                'Cable Crossovers',
                'Decline Bench Press',
                'Machine Chest Press',
                'Pec Deck',
                'Close-Grip Push Ups',
                'Incline Dumbbell Flyes',
                'Plate Press',
            ],
            Category::Biceps->value => [
                'Barbell Curl',
                'Dumbbell Curl',
                'Hammer Curl',
                'Preacher Curl',
                'Concentration Curl',
                'Cable Curl',
                'Incline Dumbbell Curl',
                'Zottman Curl',
                'Chin Ups',
                'Reverse Curl',
                'Spider Curl',
                '21s',
            ],
            Category::Triceps->value => [
                'Triceps Pushdown',
                'Overhead Triceps Extension',
                'Close Grip Bench Press',
                'Triceps Dips',
                'Skull Crushers',
                'Diamond Push Ups',
                'Cable Overhead Extension',
                'Bench Dips',
                'Single Arm Dumbbell Extension',
                'Reverse Grip Pushdown',
                'Triceps Kickback',
                'Lying Triceps Extension',
            ],
            Category::Shoulders->value => [
                'Overhead Press',
                'Dumbbell Lateral Raise',
                'Front Raise',
                'Reverse Fly',
                'Arnold Press',
                'Dumbbell Shrug',
                'Barbell Shrug',
                'Face Pull',
                'Cable Lateral Raise',
                'Upright Row',
                'Seated Dumbbell Press',
                'Military Press',
            ],
            Category::Back->value => [
                'Dead-lift',
                'Pull Ups',
                'Lat Pull-down',
                'Bent Over Row',
                'Seated Cable Row',
                'T-Bar Row',
                'Single Arm Dumbbell Row',
                'Inverted Row',
                'Good Mornings',
                'Back Extensions',
                'Wide Grip Pull Ups',
                'Machine Assisted Pull Ups',
            ],
            Category::Abs->value => [
                'Crunches',
                'Hanging Leg Raise',
                'Plank',
                'Russian Twist',
                'Bicycle Crunches',
                'Ab Wheel Rollout',
                'Leg Raise',
                'Cable Crunch',
                'Mountain Climbers',
                'Reverse Crunch',
                'V-Ups',
                'Flutter Kicks',
            ],
            Category::Legs->value => [
                'Squat',
                'Leg Press',
                'Lunges',
                'Leg Extension',
                'Leg Curl',
                'Calf Raise',
                'Goblet Squat',
                'Bulgarian Split Squat',
                'Smith Machine Squat',
                'Sumo Dead-lift',
                'Hip Thrust',
                'Glute Bridge',
            ],
            Category::Core->value => [
                'Dead Bug',
                'Bird Dog',
                'Side Plank',
                'Pall-of Press',
                'Medicine Ball Slam',
                'Wood-chopper',
                'Swiss Ball Rollout',
                'Side Bend',
                'Cable Twist',
                'Farmer Walk',
                'Kettle bell Windmill',
                'Plank with Shoulder Tap',
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
