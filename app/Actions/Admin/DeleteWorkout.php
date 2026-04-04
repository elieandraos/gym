<?php

namespace App\Actions\Admin;

use App\Models\Workout;

class DeleteWorkout
{
    public function handle(Workout $workout): void
    {
        $workout->delete();
    }
}
