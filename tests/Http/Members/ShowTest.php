<?php

use App\Http\Resources\BookingResource;
use App\Http\Resources\MemberResource;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

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
        ->assertSuccessful();
});

test('lastCompletedBooking is null when member has an active booking', function () {
    $member = User::query()->members()->whereHas('memberActiveBooking')->first();

    actingAsAdmin()
        ->get(route('admin.members.show', $member))
        ->assertInertia(fn ($page) => $page->where('lastCompletedBooking', null));
});

test('lastCompletedBooking is present when no active booking and last booking ended within 3 weeks', function () {
    $trainer = User::query()->trainers()->first();
    $member = User::factory()->create(['role' => \App\Enums\Role::Member]);

    $recentBooking = Booking::factory()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::today()->subWeeks(4),
        'end_date' => Carbon::today()->subWeeks(1),
    ]);

    actingAsAdmin()
        ->get(route('admin.members.show', $member))
        ->assertInertia(fn ($page) => $page->where('lastCompletedBooking.id', $recentBooking->id));
});

test('lastCompletedBooking is null when no active booking and last booking ended more than 3 weeks ago', function () {
    $trainer = User::query()->trainers()->first();
    $member = User::factory()->create(['role' => \App\Enums\Role::Member]);

    Booking::factory()->create([
        'member_id' => $member->id,
        'trainer_id' => $trainer->id,
        'start_date' => Carbon::today()->subWeeks(8),
        'end_date' => Carbon::today()->subWeeks(4),
    ]);

    actingAsAdmin()
        ->get(route('admin.members.show', $member))
        ->assertInertia(fn ($page) => $page->where('lastCompletedBooking', null));
});
