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
        'name' => 'Test New Exercise',
        'categories' => [Category::Core->value, Category::Abs->value],
    ];

    try {
        actingAsAdmin()
            ->post(route('admin.workouts.store'), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.workouts.index'));
    } catch (JsonException $e) {
        echo $e;
    }

    $workout = Workout::query()->where('name', 'Test New Exercise')->first();
    expect($workout)->not->toBeNull()
        ->and($workout->categories)->toBe([Category::Core->value, Category::Abs->value]);
});

test('it validates workout creation', function () {
    $data = [
        'name' => null,
        'categories' => 'invalid_not_array',
        'image' => 'not_an_image',
    ];

    actingAsAdmin()
        ->post(route('admin.workouts.store'), $data)
        ->assertSessionHasErrors([
            'name',
            'categories',
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
        'categories' => [Category::Core->value],
    ];

    try {
        actingAsAdmin()
            ->put(route('admin.workouts.update', $workout), $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.workouts.index'));
    } catch (JsonException $e) {
        echo $e;
    }

    $workout->refresh();
    expect($workout->name)->toBe('Updated Exercise Name')
        ->and($workout->categories)->toBe([Category::Core->value]);
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

test('it deletes a workout', function () {
    $workout = Workout::query()->firstOrFail();

    actingAsAdmin()
        ->delete(route('admin.workouts.destroy', $workout))
        ->assertRedirect(route('admin.workouts.index'));

    $this->assertDatabaseMissing(Workout::class, ['id' => $workout->id]);
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
