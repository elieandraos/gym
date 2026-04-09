<?php

namespace App\Actions\Admin;

use App\Models\Workout;

class CreateWorkout
{
    public function handle(array $attributes): Workout
    {
        return Workout::query()->create($attributes);
    }
}
