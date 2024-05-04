<?php

use App\Enums\Role;
use App\Http\Resources\UserResource;
use App\Models\User;

test('it lists all members', function () {
    User::factory(30)->create();
    $admin = User::factory()->create(['role' => Role::Admin]);

    $this->actingAs($admin);

    $members = User::members()->latest('id')->paginate();

    $this->get(route('admin.users.index'))
        ->assertHasResource('user', UserResource::make(User::first()))
        ->assertHasPaginatedResource('users', UserResource::collection($members))
        ->assertStatus(200);
});
