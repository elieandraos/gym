<?php

namespace App\Actions\Admin;

use App\Models\Workout;

class UpdateWorkout
{
    public function handle(Workout $workout, array $attributes): Workout
    {
        $workout->update($attributes);

        return $workout;
    }
}
