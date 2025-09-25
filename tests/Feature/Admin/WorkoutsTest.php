<?php

use App\Enums\Category;
use App\Http\Resources\WorkoutResource;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('workouts routes require authentication', function () {
    $workout = Workout::query()->firstOrFail();

    $this->get(route('admin.workouts.index'))->assertRedirect(route('login'));
    $this->get(route('admin.workouts.create'))->assertRedirect(route('login'));
    $this->post(route('admin.workouts.store'))->assertRedirect(route('login'));
    $this->get(route('admin.workouts.edit', $workout))->assertRedirect(route('login'));
    $this->put(route('admin.workouts.update', $workout))->assertRedirect(route('login'));
    $this->delete(route('admin.workouts.destroy', $workout))->assertRedirect(route('login'));
});

test('it lists all the workouts', function () {
    $workouts = Workout::query()
        ->orderBy('name')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.workouts.index'))
        ->assertHasComponent('Admin/Workouts/Index')
        ->assertHasPaginatedResource('workouts', WorkoutResource::collection($workouts))
        ->assertStatus(200);
});

test('it renders the workout create page', function () {
    actingAsAdmin()
        ->get(route('admin.workouts.create'))
        ->assertHasComponent('Admin/Workouts/Create')
        ->assertStatus(200);
});

test('it creates a workout', function () {
    $data = [
        'name' => 'Push-ups',
        'category' => Category::Core->value,
    ];

    try {
        actingAsAdmin()
            ->post(route('admin.workouts.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.workouts.index'));
    } catch (JsonException $e) {
        echo $e;
    }

    $this->assertDatabaseHas(Workout::class, $data);
});

test('it validates workout creation', function () {
    $data = [
        'name' => null,
        'category' => 'invalid_category',
        'image' => 'not_an_image',
    ];

    actingAsAdmin()
        ->post(route('admin.workouts.store'), $data)
        ->assertSessionHasErrors([
            'name',
            'category',
        ])
        ->assertStatus(302);
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
        'category' => Category::Abs->value,
    ];

    try {
        actingAsAdmin()
            ->put(route('admin.workouts.update', $workout), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.workouts.index'));
    } catch (JsonException $e) {
        echo $e;
    }

    $this->assertDatabaseHas(Workout::class, array_merge(['id' => $workout->id], $data));
});

test('it validates workout update', function () {
    $workout = Workout::query()->firstOrFail();

    $data = [
        'name' => null,
        'category' => 'invalid_category',
    ];

    actingAsAdmin()
        ->put(route('admin.workouts.update', $workout), $data)
        ->assertSessionHasErrors([
            'name',
            'category',
        ])
        ->assertStatus(302);
});

test('it deletes a workout', function () {
    $workout = Workout::query()->firstOrFail();

    actingAsAdmin()
        ->delete(route('admin.workouts.destroy', $workout))
        ->assertRedirect(route('admin.workouts.index'));

    $this->assertDatabaseMissing(Workout::class, ['id' => $workout->id]);
});
