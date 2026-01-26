<?php

use App\Enums\Category;
use App\Http\Resources\WorkoutResource;
use App\Models\Workout;

beforeEach(function () {
    setupUsersAndBookings();
});

test('index route requires authentication', function () {
    $this->get(route('admin.workouts.index'))->assertRedirect(route('login'));
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

test('it filters workouts by search', function () {
    Workout::factory()->create(['name' => 'Push-ups']);
    Workout::factory()->create(['name' => 'Pull-ups']);
    Workout::factory()->create(['name' => 'Squats']);

    $workouts = Workout::query()
        ->where('name', 'like', '%Push%')
        ->orderBy('name')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.workouts.index', ['search' => 'Push']))
        ->assertHasComponent('Admin/Workouts/Index')
        ->assertHasPaginatedResource('workouts', WorkoutResource::collection($workouts))
        ->assertStatus(200);
});

test('it filters workouts by single category', function () {
    Workout::factory()->create(['name' => 'Chest Exercise', 'categories' => [Category::Chest->value]]);
    Workout::factory()->create(['name' => 'Legs Exercise', 'categories' => [Category::Legs->value]]);
    Workout::factory()->create(['name' => 'Core Exercise', 'categories' => [Category::Core->value]]);

    $workouts = Workout::query()
        ->where(function ($q) {
            $q->whereJsonContains('categories', Category::Chest->value);
        })
        ->orderBy('name')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.workouts.index', ['categories' => [Category::Chest->value]]))
        ->assertHasComponent('Admin/Workouts/Index')
        ->assertHasPaginatedResource('workouts', WorkoutResource::collection($workouts))
        ->assertStatus(200);
});

test('it filters workouts by multiple categories', function () {
    Workout::factory()->create(['name' => 'Chest Exercise', 'categories' => [Category::Chest->value]]);
    Workout::factory()->create(['name' => 'Legs Exercise', 'categories' => [Category::Legs->value]]);
    Workout::factory()->create(['name' => 'Core Exercise', 'categories' => [Category::Core->value]]);
    Workout::factory()->create(['name' => 'Back Exercise', 'categories' => [Category::Back->value]]);

    $workouts = Workout::query()
        ->where(function ($q) {
            $q->whereJsonContains('categories', Category::Chest->value)
                ->orWhereJsonContains('categories', Category::Legs->value);
        })
        ->orderBy('name')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.workouts.index', ['categories' => [Category::Chest->value, Category::Legs->value]]))
        ->assertHasComponent('Admin/Workouts/Index')
        ->assertHasPaginatedResource('workouts', WorkoutResource::collection($workouts))
        ->assertStatus(200);
});
