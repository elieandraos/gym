<?php

use App\Http\Resources\MemberResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('index route requires authentication', function () {
    $this->get(route('admin.members.index'))->assertRedirect(route('login'));
});

test('it lists all the members', function () {
    $members = User::query()
        ->members()
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.members.index'))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', MemberResource::collection($members))
        ->assertStatus(200);
});

test('it searches members by name', function () {
    $searchTerm = 'John';
    $searchResults = User::query()
        ->members()
        ->where('name', 'like', "%{$searchTerm}%")
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.members.index', ['search' => $searchTerm]))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', MemberResource::collection($searchResults))
        ->assertHasProp('search', 'John')
        ->assertStatus(200);
});

test('it returns empty results when no members match search', function () {
    $emptyMembers = User::query()
        ->members()
        ->where('name', 'like', '%NonExistentMember%')
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.members.index', ['search' => 'NonExistentMember']))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', MemberResource::collection($emptyMembers))
        ->assertStatus(200);
});

test('it shows all members when training status is "all"', function () {
    $allMembers = User::query()
        ->members()
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.members.index', ['trainingStatus' => 'all']))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', MemberResource::collection($allMembers))
        ->assertHasProp('trainingStatus', 'all')
        ->assertStatus(200);
});

test('it filters members by active training status', function () {
    $activeMembers = User::query()
        ->members()
        ->whereHas('memberActiveBooking')
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.members.index', ['trainingStatus' => 'active']))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', MemberResource::collection($activeMembers))
        ->assertHasProp('trainingStatus', 'active')
        ->assertStatus(200);
});

test('it filters members by dormant status', function () {
    $dormantMembers = User::query()
        ->members()
        ->whereDoesntHave('memberActiveBooking')
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.members.index', ['trainingStatus' => 'dormant']))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', MemberResource::collection($dormantMembers))
        ->assertHasProp('trainingStatus', 'dormant')
        ->assertStatus(200);
});
