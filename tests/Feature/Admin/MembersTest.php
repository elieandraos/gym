<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Http\Resources\UserResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('members routes require authentication', function () {
    $member = User::query()->members()->first();

    $this->get(route('admin.members.index'))->assertRedirect(route('login'));
    $this->get(route('admin.members.create'))->assertRedirect(route('login'));
    $this->post(route('admin.members.store'))->assertRedirect(route('login'));
    $this->get(route('admin.members.show', $member))->assertRedirect(route('login'));
});

test('it lists all the members', function () {
    $members = User::query()->members()->paginate();

    actingAsAdmin()
        ->get(route('admin.members.index'))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', UserResource::collection($members))
        ->assertStatus(200);
});

test('it renders the member create page', function () {
    actingAsAdmin()
        ->get(route('admin.members.create'))
        ->assertHasComponent('Admin/Members/Create')
        ->assertStatus(200);
});

test('it creates a member', function () {
    $data = [
        'name' => 'Elie A',
        'email' => 'elie@liftstation.fitness',
        'registration_date' => '2024-01-05',
        'in_house' => 1,
        'gender' => Gender::Male->value,
        'weight' => 75,
        'height' => 185,
        'birthdate' => '1985-10-31',
        'blood_type' => BloodType::OPlus->value,
        'phone_number' => '00961 3 140 625',
        'instagram_handle' => 'elieandraos',
        'address' => 'aa',
        'emergency_contact' => 'dd',
    ];

    actingAsAdmin()
        ->post(route('admin.members.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.index'));

    $this->assertDatabaseHas(User::class, $data);
});

test('it validates member creation', function () {
    $data = [
        'name' => null,
        'email' => 'elie',
        'registration_date' => null,
        'in_house' => null,
        'gender' => null,
        'weight' => null,
        'height' => null,
        'birthdate' => null,
        'blood_type' => null,
        'phone_number' => null,
        'instagram_handle' => '',
        'address' => '',
        'emergency_contact' => '',
    ];

    actingAsAdmin()
        ->post(route('admin.members.store'), $data)
        ->assertSessionHasErrors([
            'name',
            'email',
            'registration_date',
            'in_house',
            'gender',
            'weight',
            'height',
            'birthdate',
            'blood_type',
        ])
        ->assertStatus(302);
});

test('it shows member information', function () {
    $member = User::query()->members()->first();
    $member->load([
        'memberBookings' => fn ($q) => $q->active()->with(['trainer', 'bookingSlots']),
    ]);

    actingAsAdmin()
        ->get(route('admin.members.show', $member))
        ->assertHasComponent('Admin/Members/Show')
        ->assertHasResource('member', UserResource::make($member))
        ->assertStatus(200);
});
