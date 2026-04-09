<?php

use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('delete route requires authentication', function () {
    $workout = Workout::query()->firstOrFail();

    $this->delete(route('admin.workouts.destroy', $workout))->assertRedirect(route('login'));
});

test('it deletes a workout', function () {
    $workout = Workout::query()->firstOrFail();

    actingAsAdmin()
        ->delete(route('admin.workouts.destroy', $workout))
        ->assertRedirect(route('admin.workouts.index'));
});
