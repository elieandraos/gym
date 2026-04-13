<?php

use App\Enums\Role;
use App\Models\User;

test('member cannot access admin routes', function () {
    $member = User::factory()->create(['role' => Role::Member]);

    $this->actingAs($member)
        ->get(route('admin.members.index'))
        ->assertForbidden();
});

test('trainer cannot access admin routes', function () {
    $trainer = User::factory()->create(['role' => Role::Trainer]);

    $this->actingAs($trainer)
        ->get(route('admin.members.index'))
        ->assertForbidden();
});

test('admin can access admin routes', function () {
    actingAsAdmin()
        ->get(route('admin.members.index'))
        ->assertOk();
});
