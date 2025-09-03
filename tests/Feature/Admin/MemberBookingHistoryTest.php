<?php

use App\Http\Resources\MemberResource;
use App\Models\User;
use Illuminate\Http\Request;

beforeEach(function () {
    setupUsersAndBookings();
});

test('member booking history route requires authentication', function () {
    $member = User::query()->members()->first();

    $this->get(route('admin.members.bookings.history', $member))->assertRedirect(route('login'));
});

test('it displays member booking history', function () {
    $member = User::query()->members()->first();

    $completedBookings = $member->memberCompletedBookings;
    expect($completedBookings->count())->toBeGreaterThan(0, 'Member should have completed bookings for this test');

    $member->load('memberCompletedBookings');

    actingAsAdmin()
        ->get(route('admin.members.bookings.history', $member))
        ->assertHasComponent('Admin/MemberBookingsHistory/Index')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);
});
