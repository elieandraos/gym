<?php

use App\Enums\Role;
use App\Http\Resources\UserResource;
use App\Models\User;

beforeEach(function () {
    User::factory()->count(10)->create(['role' => Role::Member]);
    User::factory()->count(2)->create(['role' => Role::Trainer]);
});

test('it lists all members', function () {
    $users = User::query()->members()->paginate();

    actingAsAdmin()
        ->get(route('admin.users.index', [ 'role' => Role::Member]))
        ->assertHasPaginatedResource('users', UserResource::collection($users))
        ->assertHasComponent('Admin/Users/Index')
        ->assertStatus(200);
});

test('it lists all trainers', function () {
    $users = User::query()->trainers()->paginate();

    actingAsAdmin()
        ->get(route('admin.users.index', [ 'role' => Role::Trainer]))
        ->assertHasPaginatedResource('users', UserResource::collection($users))
        ->assertHasComponent('Admin/Users/Index')
        ->assertStatus(200);
});
