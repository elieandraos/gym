<?php

use App\Enums\Role;
use App\Http\Resources\TrainerResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('index route requires authentication', function () {
    $this->get(route('admin.trainers.index'))->assertRedirect(route('login'));
});

test('it lists all the trainers', function () {
    $trainers = User::query()
        ->trainers()
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.trainers.index'))
        ->assertHasComponent('Admin/Trainers/Index')
        ->assertHasPaginatedResource('trainers', TrainerResource::collection($trainers))
        ->assertStatus(200);
});

test('it filters trainers by search', function () {
    User::factory()->create([
        'role' => Role::Trainer->value,
        'name' => 'John Trainer',
    ]);
    User::factory()->create([
        'role' => Role::Trainer->value,
        'name' => 'Jane Coach',
    ]);
    User::factory()->create([
        'role' => Role::Trainer->value,
        'name' => 'Mike Fitness',
    ]);

    $trainers = User::query()
        ->trainers()
        ->where('name', 'like', '%John%')
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.trainers.index', ['search' => 'John']))
        ->assertHasComponent('Admin/Trainers/Index')
        ->assertHasPaginatedResource('trainers', TrainerResource::collection($trainers))
        ->assertStatus(200);
});
