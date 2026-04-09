<?php

use App\Actions\Admin\DeleteWorkout;
use App\Models\Workout;

test('it deletes the workout from the database', function () {
    $workout = Workout::factory()->create();

    app(DeleteWorkout::class)->handle($workout);

    $this->assertDatabaseMissing(Workout::class, ['id' => $workout->id]);
});
