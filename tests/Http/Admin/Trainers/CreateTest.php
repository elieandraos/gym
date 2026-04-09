<?php

use App\Enums\BloodType;
use App\Enums\Gender;

beforeEach(function () {
    setupUsersAndBookings();
});

test('create routes require authentication', function () {
    $this->get(route('admin.trainers.create'))->assertRedirect(route('login'));
    $this->post(route('admin.trainers.store'))->assertRedirect(route('login'));
});

test('it renders the trainer create page', function () {
    actingAsAdmin()
        ->get(route('admin.trainers.create'))
        ->assertHasComponent('Admin/Trainers/Create')
        ->assertStatus(200);
});

test('it creates a trainer', function () {
    $data = [
        'name' => 'Elie Trainer',
        'email' => 'elie.trainer@liftstation.fitness',
        'registration_date' => '2024-02-01',
        'gender' => Gender::Male->value,
        'weight' => 80,
        'height' => 180,
        'birthdate' => '1980-01-01',
        'blood_type' => BloodType::OPlus->value,
        'phone_number' => '00961 3 000 000',
        'instagram_handle' => 'elie.trainer',
        'address' => 'somewhere',
        'emergency_contact' => 'someone',
        'color' => 'bg-blue-50',
    ];

    actingAsAdmin()
        ->post(route('admin.trainers.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.trainers.index'));
});

test('it validates trainer creation', function () {
    $data = [
        'name' => null,
        'email' => 'invalid-email',
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
        ->post(route('admin.trainers.store'), $data)
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
