<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Http\Resources\TrainerResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('update routes require authentication', function () {
    $trainer = User::query()->trainers()->first();

    $this->get(route('admin.trainers.edit', $trainer))->assertRedirect(route('login'));
    $this->put(route('admin.trainers.update', $trainer))->assertRedirect(route('login'));
});

test('it renders the trainer edit page', function () {
    $trainer = User::query()->trainers()->first();

    actingAsAdmin()
        ->get(route('admin.trainers.edit', $trainer))
        ->assertHasComponent('Admin/Trainers/Edit')
        ->assertHasResource('trainer', TrainerResource::make($trainer))
        ->assertStatus(200);
});

test('it updates a trainer', function () {
    $trainer = User::query()->trainers()->first();

    $updatedData = [
        'name' => 'Updated Trainer Name',
        'email' => 'updated.trainer@liftstation.fitness',
        'registration_date' => $trainer->registration_date,
        'in_house' => $trainer->in_house,
        'gender' => Gender::Female->value,
        'weight' => 70,
        'height' => 175,
        'birthdate' => '1985-12-20',
        'blood_type' => BloodType::BPlus->value,
        'phone_number' => '00961 3 111 222',
        'instagram_handle' => 'updated_trainer',
        'address' => 'Updated Trainer Address',
        'emergency_contact' => 'Updated Trainer Contact',
        'color' => 'bg-purple-100',
    ];

    actingAsAdmin()
        ->put(route('admin.trainers.update', $trainer), $updatedData)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.trainers.show', $trainer));
});

test('it validates trainer update', function () {
    $trainer = User::query()->trainers()->first();

    $invalidData = [
        'name' => null,
        'email' => 'invalid-email',
        'registration_date' => null,
        'gender' => null,
        'weight' => null,
        'height' => null,
        'birthdate' => null,
        'blood_type' => null,
        'phone_number' => null,
    ];

    actingAsAdmin()
        ->put(route('admin.trainers.update', $trainer), $invalidData)
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

test('it allows updating trainer with same email', function () {
    $trainer = User::query()->trainers()->first();

    $updatedData = [
        'name' => 'Updated Trainer Name',
        'email' => $trainer->email, // Keep same email
        'registration_date' => $trainer->registration_date,
        'gender' => $trainer->gender,
        'weight' => 85,
        'height' => 185,
        'birthdate' => $trainer->birthdate,
        'blood_type' => $trainer->blood_type,
        'phone_number' => $trainer->phone_number,
        'instagram_handle' => $trainer->instagram_handle,
        'address' => $trainer->address,
        'emergency_contact' => $trainer->emergency_contact,
        'color' => $trainer->color,
    ];

    actingAsAdmin()
        ->put(route('admin.trainers.update', $trainer), $updatedData)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.trainers.show', $trainer));
});

test('it prevents updating trainer with duplicate email', function () {
    $trainer1 = User::query()->trainers()->first();
    $trainer2 = User::query()->trainers()->skip(1)->first();

    $updatedData = [
        'name' => 'Updated Trainer Name',
        'email' => $trainer2->email, // Try to use another trainer's email
        'registration_date' => $trainer1->registration_date,
        'in_house' => $trainer1->in_house,
        'gender' => $trainer1->gender,
        'weight' => $trainer1->weight,
        'height' => $trainer1->height,
        'birthdate' => $trainer1->birthdate,
        'blood_type' => $trainer1->blood_type,
        'phone_number' => $trainer1->phone_number,
        'instagram_handle' => $trainer1->instagram_handle,
        'address' => $trainer1->address,
        'emergency_contact' => $trainer1->emergency_contact,
        'color' => $trainer1->color,
    ];

    actingAsAdmin()
        ->put(route('admin.trainers.update', $trainer1), $updatedData)
        ->assertSessionHasErrors(['email'])
        ->assertStatus(302);
});
