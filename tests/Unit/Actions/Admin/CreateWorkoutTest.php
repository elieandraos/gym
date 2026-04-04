<?php

use App\Actions\Admin\CreateWorkout;
use App\Models\Workout;

test('it creates a workout in the database', function () {
    $result = (new CreateWorkout)->handle([
        'name' => 'Bench Press',
        'categories' => ['chest'],
        'image' => 'https://example.com/image.jpg',
    ]);

    $this->assertDatabaseHas(Workout::class, ['name' => 'Bench Press']);
    expect($result)->toBeInstanceOf(Workout::class);
});
