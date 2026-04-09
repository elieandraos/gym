<?php

use App\Enums\Category;

beforeEach(function () {
    setupUsersAndBookings();
});

test('create routes require authentication', function () {
    $this->get(route('admin.workouts.create'))->assertRedirect(route('login'));
    $this->post(route('admin.workouts.store'))->assertRedirect(route('login'));
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

    actingAsAdmin()
        ->post(route('admin.workouts.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.workouts.index'));
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
