<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('create routes require authentication', function () {
    $this->get(route('admin.members.create'))->assertRedirect(route('login'));
    $this->post(route('admin.members.store'))->assertRedirect(route('login'));
});

test('it renders the member create page', function () {
    actingAsAdmin()
        ->get(route('admin.members.create'))
        ->assertHasComponent('Admin/Members/Create')
        ->assertStatus(200);
});

test('it creates a member and redirects to celebration page', function () {
    $data = [
        'name' => 'Elie A',
        'email' => 'elie@liftstation.fitness',
        'registration_date' => '2024-01-05',
        'gender' => Gender::Male->value,
        'weight' => 75,
        'height' => 185,
        'birthdate' => '1985-10-31',
        'blood_type' => BloodType::OPlus->value,
        'phone_number' => '00961 3 140 625',
        'instagram_handle' => 'elieandraos',
        'address' => 'aa',
        'emergency_contact' => 'dd',
        'color' => 'bg-green-50',
    ];

    $response = actingAsAdmin()
        ->post(route('admin.members.store'), $data)
        ->assertSessionHasNoErrors();

    $member = User::query()->where('email', 'elie@liftstation.fitness')->first();
    $response->assertRedirect(route('admin.member-created', $member));
});

test('it validates member creation', function () {
    $data = [
        'name' => null,
        'email' => 'elie',
        'registration_date' => null,
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
            'gender',
            'weight',
            'height',
            'birthdate',
            'blood_type',
        ])
        ->assertStatus(302);
});
