<?php

use App\Http\Resources\UserResource;
use App\Models\User;

test('it lists all members', function () {
    $members = User::members()->latest('id')->paginate();

    actingAsAdmin()
        ->get(route('admin.users.index'))
        ->assertHasResource('user', UserResource::make(User::first()))
        ->assertHasPaginatedResource('users', UserResource::collection($members))
        ->assertStatus(200);
});
