<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\Role;
use App\Http\Resources\UserResource;
use App\Models\User;

beforeEach(function () {
    User::factory()->count(1)->create(['role' => Role::Member]);
    User::factory()->count(1)->create(['role' => Role::Trainer]);
});

test('it requires authentication', function () {
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    $this->post(route('admin.users.store'))->assertRedirect(route('login'));
});

test('it lists all members', function () {
    $users = User::query()->members()->paginate();

    actingAsAdmin()
        ->get(route('admin.users.index', ['role' => Role::Member]))
        ->assertHasPaginatedResource('users', UserResource::collection($users))
        ->assertHasComponent('Admin/Users/Index')
        ->assertStatus(200);
});

test('it lists all trainers', function () {
    $users = User::query()->trainers()->paginate();

    actingAsAdmin()
        ->get(route('admin.users.index', ['role' => Role::Trainer]))
        ->assertHasPaginatedResource('users', UserResource::collection($users))
        ->assertHasComponent('Admin/Users/Index')
        ->assertStatus(200);
});

test('it validates request before creating user', function () {
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
        'role' => null,
    ];

    actingAsAdmin()
        ->post(route('admin.users.store'), $data)
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
            'role',
        ])
        ->assertStatus(302);
});

test('it creates a user', function () {
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
        'role' => Role::Member->value,
    ];

    actingAsAdmin()
        ->post(route('admin.users.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.users.index', ['role' => Role::Member->value]));

    $this->assertDatabaseHas(User::class, $data);
});

test('it shows user information', function () {
    $user = User::query()->members()->first();

    actingAsAdmin()
        ->get(route('admin.users.show', $user))
        ->assertHasResource('user', UserResource::make($user))
        ->assertHasComponent('Admin/Users/Show')
        ->assertStatus(200);
});