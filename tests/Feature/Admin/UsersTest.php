<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\Role;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;

beforeEach(function () {
    $members = User::factory()->count(1)->create(['role' => Role::Member]);
    $trainers = User::factory()->count(1)->create(['role' => Role::Trainer]);

    $members->each(function ($user) use ($trainers) {
        // Create active booking
        $booking = Booking::factory()->active()->create([
            'member_id' => $user->id,
            'trainer_id' => $trainers->random()->id,
        ]);

        BookingSlot::factory($booking->nb_sessions)->forBooking($booking)->create();
    });
});

test('it requires authentication', function () {
    $user = User::query()->members()->first();

    $this->get(route('admin.users.index', Role::Member->value))->assertRedirect(route('login'));
    $this->get(route('admin.users.create', Role::Member->value))->assertRedirect(route('login'));
    $this->post(route('admin.users.store'))->assertRedirect(route('login'));
    $this->get(route('admin.users.show', [$user, $user->role]))->assertRedirect(route('login'));
});

test('it lists all the members', function () {
    $users = User::query()->members()->paginate();
    $role = Role::Member->value;

    actingAsAdmin()
        ->get(route('admin.users.index', ['role' => $role]))
        ->assertHasComponent('Admin/Users/Index')
        ->assertHasPaginatedResource('users', UserResource::collection($users))
        ->assertHasProp('role', $role)
        ->assertStatus(200);
});

test('it lists all the trainers', function () {
    $users = User::query()->trainers()->paginate();
    $role = Role::Trainer->value;

    actingAsAdmin()
        ->get(route('admin.users.index', ['role' => $role]))
        ->assertHasComponent('Admin/Users/Index')
        ->assertHasPaginatedResource('users', UserResource::collection($users))
        ->assertHasProp('role', $role)
        ->assertStatus(200);
});

test('it renders the member or trainer create page', function() {
    $role = Role::Member->value;

    actingAsAdmin()
        ->get(route('admin.users.create', ['role' => $role]))
        ->assertHasComponent('Admin/Users/Create')
        ->assertHasProp('role', $role)
        ->assertStatus(200);
});

test('it creates a member or trainer', function () {
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

test('it validates request before creating member or trainer', function () {
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

test('it shows trainer information', function () {
    $trainer = User::query()->trainers()->first();
    $trainer->loadActiveBookingsWithSlots();

    actingAsAdmin()
        ->get(route('admin.users.show', [$trainer, Role::Trainer->value]))
        ->assertHasComponent('Admin/Users/Show')
        ->assertHasResource('user', UserResource::make($trainer))
        ->assertStatus(200);
});

test('it shows member information', function () {
    $member = User::query()->members()->first();
    $member->loadActiveBookingsWithSlots();

    actingAsAdmin()
        ->get(route('admin.users.show', [$member, Role::Member->value]))
        ->assertHasComponent('Admin/Users/Show')
        ->assertHasResource('user', UserResource::make($member))
        ->assertStatus(200);
});
