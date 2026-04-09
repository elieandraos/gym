<?php

use App\Actions\Admin\UpdateWorkout;
use App\Models\Workout;

test('it updates the workout fields in the database', function () {
    $workout = Workout::factory()->create(['name' => 'Original']);

    app(UpdateWorkout::class)->handle($workout, ['name' => 'Updated']);

    $this->assertDatabaseHas(Workout::class, ['id' => $workout->id, 'name' => 'Updated']);
});

test('it returns the updated Workout instance', function () {
    $workout = Workout::factory()->create(['name' => 'Before']);

    $result = app(UpdateWorkout::class)->handle($workout, ['name' => 'After']);

    expect($result)->toBeInstanceOf(Workout::class)
        ->and($result->name)->toBe('After');
});
