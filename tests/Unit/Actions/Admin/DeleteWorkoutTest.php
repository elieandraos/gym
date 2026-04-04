<?php

use App\Actions\Admin\DeleteWorkout;
use App\Models\Workout;

test('it deletes the workout from the database', function () {
    $workout = Workout::factory()->create();

    (new DeleteWorkout)->handle($workout);

    $this->assertDatabaseMissing(Workout::class, ['id' => $workout->id]);
});
