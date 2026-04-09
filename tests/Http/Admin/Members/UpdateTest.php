<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Http\Resources\MemberResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('edit/update routes require authentication', function () {
    $member = User::query()->members()->first();

    $this->get(route('admin.members.edit', $member))->assertRedirect(route('login'));
    $this->put(route('admin.members.update', $member))->assertRedirect(route('login'));
});

test('it renders the member edit page', function () {
    $member = User::query()->members()->first();

    actingAsAdmin()
        ->get(route('admin.members.edit', $member))
        ->assertHasComponent('Admin/Members/Edit')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);
});

test('it updates a member', function () {
    $member = User::query()->members()->first();

    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated@liftstation.fitness',
        'registration_date' => $member->registration_date,
        'in_house' => $member->in_house,
        'gender' => Gender::Female->value,
        'weight' => 65,
        'height' => 170,
        'birthdate' => '1990-05-15',
        'blood_type' => BloodType::APlus->value,
        'phone_number' => '00961 3 999 888',
        'instagram_handle' => 'updated_handle',
        'address' => 'Updated Address',
        'emergency_contact' => 'Updated Contact',
        'color' => 'bg-pink-50',
    ];

    actingAsAdmin()
        ->put(route('admin.members.update', $member), $updatedData)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', $member));
});

test('it validates member update', function () {
    $member = User::query()->members()->first();

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
        ->put(route('admin.members.update', $member), $invalidData)
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

test('it allows updating member with same email', function () {
    $member = User::query()->members()->first();

    $updatedData = [
        'name' => 'Updated Name',
        'email' => $member->email, // Keep same email
        'registration_date' => $member->registration_date,
        'gender' => $member->gender,
        'weight' => 75,
        'height' => 180,
        'birthdate' => $member->birthdate,
        'blood_type' => $member->blood_type,
        'phone_number' => $member->phone_number,
        'instagram_handle' => $member->instagram_handle,
        'address' => $member->address,
        'emergency_contact' => $member->emergency_contact,
        'color' => $member->color,
    ];

    actingAsAdmin()
        ->put(route('admin.members.update', $member), $updatedData)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', $member));
});

test('it prevents updating member with duplicate email', function () {
    $member1 = User::query()->members()->first();
    $member2 = User::query()->members()->skip(1)->first();

    $updatedData = [
        'name' => 'Updated Name',
        'email' => $member2->email, // Try to use another member's email
        'registration_date' => $member1->registration_date,
        'in_house' => $member1->in_house,
        'gender' => $member1->gender,
        'weight' => $member1->weight,
        'height' => $member1->height,
        'birthdate' => $member1->birthdate,
        'blood_type' => $member1->blood_type,
        'phone_number' => $member1->phone_number,
        'instagram_handle' => $member1->instagram_handle,
        'address' => $member1->address,
        'emergency_contact' => $member1->emergency_contact,
        'color' => $member1->color,
    ];

    actingAsAdmin()
        ->put(route('admin.members.update', $member1), $updatedData)
        ->assertSessionHasErrors(['email'])
        ->assertStatus(302);
});
