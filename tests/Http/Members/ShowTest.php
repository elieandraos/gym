<?php

use App\Http\Resources\MemberResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('show route requires authentication', function () {
    $member = User::query()->members()->first();

    $this->get(route('admin.members.show', $member))->assertRedirect(route('login'));
});

test('it shows member information', function () {
    $member = User::query()->members()->first();
    $member->load([
        'memberActiveBooking.bookingSlots' => function ($query) {
            $query->orderBy('start_time');
        },
        'memberScheduledBookings',
        'lastBodyComposition',
    ]);

    actingAsAdmin()
        ->get(route('admin.members.show', $member))
        ->assertHasComponent('Admin/Members/Show')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);
});
