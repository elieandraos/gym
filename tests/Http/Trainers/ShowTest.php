<?php

use App\Http\Resources\TrainerResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('show route requires authentication', function () {
    $trainer = User::query()->trainers()->first();

    $this->get(route('admin.trainers.show', $trainer))->assertRedirect(route('login'));
});

test('it shows trainer information', function () {
    $trainer = User::query()->trainers()->first();

    actingAsAdmin()
        ->get(route('admin.trainers.show', $trainer))
        ->assertHasComponent('Admin/Trainers/Show')
        ->assertHasResource('trainer', TrainerResource::make($trainer))
        ->assertStatus(200);
});
