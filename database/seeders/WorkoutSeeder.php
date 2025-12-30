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
            // Legs + Glutes (compound exercises)
            ['name' => 'Barbell Squats', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Smith Squats', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Leg Press', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Lunges', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Bulgarian Split Squats', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Step-Ups', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Hip Thrusts', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Romanian Deadlifts', 'categories' => [Category::Legs->value, Category::Glutes->value, Category::Back->value]],
            ['name' => 'Sumo Deadlifts', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Single Leg Reverse Lunges', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Single Leg Bridges', 'categories' => [Category::Glutes->value, Category::Legs->value]],
            ['name' => 'Smith rdl', 'categories' => [Category::Legs->value, Category::Glutes->value, Category::Back->value]],
            ['name' => 'Barbell rdl', 'categories' => [Category::Legs->value, Category::Glutes->value, Category::Back->value]],
            ['name' => 'Db RDL', 'categories' => [Category::Legs->value, Category::Glutes->value, Category::Back->value]],
            ['name' => 'Bulgarian split squats single handed', 'categories' => [Category::Legs->value, Category::Glutes->value]],
            ['name' => 'Stiff deadlift barbell', 'categories' => [Category::Legs->value, Category::Glutes->value, Category::Back->value]],
            ['name' => 'Bulgarian + PULSE', 'categories' => [Category::Legs->value, Category::Glutes->value]],

            // Legs only (isolation)
            ['name' => 'Leg Extensions', 'categories' => [Category::Legs->value]],
            ['name' => 'Leg Curls', 'categories' => [Category::Legs->value]],
            ['name' => 'Calf Raises Standing', 'categories' => [Category::Legs->value]],
            ['name' => 'Abductors', 'categories' => [Category::Legs->value]],
            ['name' => 'Wallsit', 'categories' => [Category::Legs->value]],

            // Legs + Biceps (Wall sit + biceps combines both)
            ['name' => 'Wall sit + biceps', 'categories' => [Category::Legs->value, Category::Biceps->value]],

            // Glutes + Back
            ['name' => 'Glutes Hyperextension', 'categories' => [Category::Glutes->value, Category::Back->value]],

            // Glutes only (isolation)
            ['name' => 'Cable Kickbacks', 'categories' => [Category::Glutes->value]],
            ['name' => 'Glute Abductions', 'categories' => [Category::Glutes->value]],

            // Biceps only
            ['name' => 'Barbell Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Dumbbell Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Preacher Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Cable Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Concentration Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Hammer Curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Spider db curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Hammer to curls', 'categories' => [Category::Biceps->value]],
            ['name' => 'Biceps db curls', 'categories' => [Category::Biceps->value]],

            // Back + Biceps
            ['name' => 'Chin-Ups', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Pull-Ups', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Lat Pulldowns', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Barbell Or Dumbbell Rows', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Seated Rows', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Kettlebell Rows', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Close grip rows', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Mid stance rows', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Medium stance rows', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Narrow pull down', 'categories' => [Category::Back->value, Category::Biceps->value]],
            ['name' => 'Landmine Wide stance Mid back rows low to high', 'categories' => [Category::Back->value, Category::Biceps->value]],

            // Triceps + Chest
            ['name' => 'Dips', 'categories' => [Category::Triceps->value, Category::Chest->value]],
            ['name' => 'Chest Dips', 'categories' => [Category::Chest->value, Category::Triceps->value]],
            ['name' => 'Close-Grip Bench Press', 'categories' => [Category::Chest->value, Category::Triceps->value]],

            // Triceps only
            ['name' => 'Cable Pushdowns', 'categories' => [Category::Triceps->value]],
            ['name' => 'Overhead Dumbbell Extensions', 'categories' => [Category::Triceps->value]],
            ['name' => 'Skull Crushers', 'categories' => [Category::Triceps->value]],
            ['name' => 'Cable Overhead Rope', 'categories' => [Category::Triceps->value]],
            ['name' => 'Rope triceps push down', 'categories' => [Category::Triceps->value]],

            // Triceps + Core (Skull crusher to toe touch combines both)
            ['name' => 'Skull crusher to toe touch (crunches)', 'categories' => [Category::Triceps->value, Category::Core->value]],

            // Shoulders + Chest
            ['name' => 'Dumbbell Press', 'categories' => [Category::Chest->value, Category::Shoulders->value]],
            ['name' => 'Arnold Press', 'categories' => [Category::Shoulders->value, Category::Chest->value]],
            ['name' => 'Incline db press', 'categories' => [Category::Chest->value, Category::Shoulders->value]],

            // Shoulders + Triceps
            ['name' => 'Overhead Press Dumbbell', 'categories' => [Category::Shoulders->value, Category::Triceps->value]],
            ['name' => 'Military Press', 'categories' => [Category::Shoulders->value, Category::Triceps->value]],
            ['name' => 'Db shoulder press', 'categories' => [Category::Shoulders->value, Category::Triceps->value]],

            // Shoulders + Back
            ['name' => 'Face Pulls', 'categories' => [Category::Back->value, Category::Shoulders->value]],
            ['name' => 'Upright Rows', 'categories' => [Category::Shoulders->value, Category::Back->value]],
            ['name' => 'Cable reverse flies', 'categories' => [Category::Back->value, Category::Shoulders->value]],

            // Shoulders only
            ['name' => 'Smith Shoulder Press', 'categories' => [Category::Shoulders->value]],
            ['name' => 'Lateral Raises', 'categories' => [Category::Shoulders->value]],
            ['name' => 'Front Raises', 'categories' => [Category::Shoulders->value]],
            ['name' => 'Rear Delt Fly', 'categories' => [Category::Shoulders->value]],
            ['name' => 'Laying lateral raises cables', 'categories' => [Category::Shoulders->value]],
            ['name' => 'Db overhead front raise', 'categories' => [Category::Shoulders->value]],

            // Chest + Triceps
            ['name' => 'Barbell Bench Press', 'categories' => [Category::Chest->value, Category::Triceps->value]],
            ['name' => 'Smith Chest Press', 'categories' => [Category::Chest->value, Category::Triceps->value]],
            ['name' => 'Push-Ups', 'categories' => [Category::Chest->value, Category::Triceps->value]],
            ['name' => 'Incline bench press', 'categories' => [Category::Chest->value, Category::Triceps->value]],
            ['name' => 'Cable flat press', 'categories' => [Category::Chest->value, Category::Triceps->value]],

            // Chest only
            ['name' => 'Cable Fly', 'categories' => [Category::Chest->value]],
            ['name' => 'Flat Cable chest flies', 'categories' => [Category::Chest->value]],
            ['name' => 'Incline chest cable flies', 'categories' => [Category::Chest->value]],
            ['name' => 'Decline cable flies', 'categories' => [Category::Chest->value]],

            // Back + Legs + Glutes (deadlifts)
            ['name' => 'Deadlifts', 'categories' => [Category::Back->value, Category::Legs->value, Category::Glutes->value]],

            // Back only
            ['name' => 'Straight Arm Pulldown', 'categories' => [Category::Back->value]],

            // Core + Abs
            ['name' => 'Crunches', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Sit-Ups', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Cable Crunch', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Leg Raises', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Hanging Knee Raises', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Reverse Crunches', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Russian Twists', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Cable Woodchoppers', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Plank Holds', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Dragon flag', 'categories' => [Category::Core->value, Category::Abs->value]],
            ['name' => 'Side abs', 'categories' => [Category::Core->value, Category::Abs->value]],

            // Core only
            ['name' => 'Side Plank', 'categories' => [Category::Core->value]],
        ];

        foreach ($workouts as $workout) {
            Workout::query()->create($workout);
        }
    }
}
