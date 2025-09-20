<?php

use App\Http\Resources\MemberResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('member personal info route requires authentication', function () {
    $member = User::query()->members()->first();

    $this->get(route('admin.members.personal-info', $member))->assertRedirect(route('login'));
});

test('it shows member personal information', function () {
    $member = User::query()->members()->first();

    actingAsAdmin()
        ->get(route('admin.members.personal-info', $member))
        ->assertHasComponent('Admin/Members/PersonalInfo')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);
});