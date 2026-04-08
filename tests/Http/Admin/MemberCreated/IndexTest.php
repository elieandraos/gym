<?php

use App\Http\Resources\MemberResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('it shows member creation celebration page', function () {
    $member = User::query()->members()->first();

    actingAsAdmin()
        ->get(route('admin.member-created', $member))
        ->assertHasComponent('Admin/MemberCreated/Index')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);
});
