<?php

use App\Enums\Category;
use App\Http\Resources\WorkoutResource;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('update routes require authentication', function () {
    $workout = Workout::query()->firstOrFail();

    $this->get(route('admin.workouts.edit', $workout))->assertRedirect(route('login'));
    $this->put(route('admin.workouts.update', $workout))->assertRedirect(route('login'));
});

test('it renders the workout edit page', function () {
    $workout = Workout::query()->firstOrFail();

    actingAsAdmin()
        ->get(route('admin.workouts.edit', $workout))
        ->assertHasComponent('Admin/Workouts/Edit')
        ->assertHasResource('workout', WorkoutResource::make($workout))
        ->assertStatus(200);
});

test('it updates a workout', function () {
    $workout = Workout::query()->firstOrFail();

    $data = [
        'name' => 'Updated Exercise Name',
        'categories' => [Category::Core->value],
    ];

    actingAsAdmin()
        ->put(route('admin.workouts.update', $workout), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.workouts.index'));
});

test('it validates workout update', function () {
    $workout = Workout::query()->firstOrFail();

    $data = [
        'name' => null,
        'categories' => 'invalid_not_array',
    ];

    actingAsAdmin()
        ->put(route('admin.workouts.update', $workout), $data)
        ->assertSessionHasErrors([
            'name',
            'categories',
        ])
        ->assertStatus(302);
});
