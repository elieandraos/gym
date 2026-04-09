<?php

use App\Enums\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    User::factory()->count(3)->create(['role' => Role::Trainer]);
});

test('show route requires authentication', function () {
    $this->get(route('admin.settings.edit'))->assertRedirect(route('login'));
});

test('admin can view settings page', function () {
    actingAsAdmin()
        ->get(route('admin.settings.edit'))
        ->assertHasComponent('Admin/Settings/Index')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('trainers')
            ->has('settings')
            ->has('settings.calendar')
            ->has('settings.notifications')
        );
});
