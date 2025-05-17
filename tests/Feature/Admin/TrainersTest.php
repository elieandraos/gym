<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Http\Resources\UserResource;
use App\Models\User;

beforeEach(function () {
    setupUsersAndBookings();
});

test('trainers routes require authentication', function () {
    $trainer = User::query()->trainers()->first();

    $this->get(route('admin.trainers.index'))->assertRedirect(route('login'));
    $this->get(route('admin.trainers.create'))->assertRedirect(route('login'));
    $this->post(route('admin.trainers.store'))->assertRedirect(route('login'));
    $this->get(route('admin.trainers.show', $trainer))->assertRedirect(route('login'));
});

test('it lists all the trainers', function () {
    $trainers = User::query()->trainers()->paginate();

    actingAsAdmin()
        ->get(route('admin.trainers.index'))
        ->assertHasComponent('Admin/Trainers/Index')
        ->assertHasPaginatedResource('trainers', UserResource::collection($trainers))
        ->assertStatus(200);
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
        'in_house' => 1,
        'gender' => Gender::Male->value,
        'weight' => 80,
        'height' => 180,
        'birthdate' => '1980-01-01',
        'blood_type' => BloodType::OPlus->value,
        'phone_number' => '00961 3 000 000',
        'instagram_handle' => 'elietrainer',
        'address' => 'somewhere',
        'emergency_contact' => 'someone'
    ];

    actingAsAdmin()
        ->post(route('admin.trainers.store'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.trainers.index'));

    $this->assertDatabaseHas(User::class, $data);
});

test('it validates trainer creation', function () {
    $data = [
        'name' => null,
        'email' => 'invalid-email',
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
        ->post(route('admin.trainers.store'), $data)
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

test('it shows trainer information', function () {
    $trainer = User::query()->trainers()->first();
    $trainer->load([
        'trainerBookings' => fn ($q) => $q->active()->with(['member', 'bookingSlots']),
    ]);

    actingAsAdmin()
        ->get(route('admin.trainers.show', $trainer))
        ->assertHasComponent('Admin/Trainers/Show')
        ->assertHasResource('trainer', UserResource::make($trainer))
        ->assertStatus(200);
});
