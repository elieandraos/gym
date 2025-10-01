<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Http\Resources\MemberResource;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    setupUsersAndBookings();
});

test('members routes require authentication', function () {
    $member = User::query()->members()->first();

    $this->get(route('admin.members.index'))->assertRedirect(route('login'));
    $this->get(route('admin.members.create'))->assertRedirect(route('login'));
    $this->post(route('admin.members.store'))->assertRedirect(route('login'));
    $this->get(route('admin.members.show', $member))->assertRedirect(route('login'));
    $this->get(route('admin.members.edit', $member))->assertRedirect(route('login'));
    $this->put(route('admin.members.update', $member))->assertRedirect(route('login'));
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
        'color' => 'bg-green-50',
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
        'memberActiveBooking.bookingSlots' => function ($query) {
            $query->orderBy('start_time')
                ->with(['bookingSlotWorkouts.workout']);
        },
        'memberScheduledBookings',
    ]);

    actingAsAdmin()
        ->get(route('admin.members.show', $member))
        ->assertHasComponent('Admin/Members/Show')
        ->assertHasResource('member', MemberResource::make($member))
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

test('it filters members by active training status', function () {
    $activeTrainingMembers = User::query()
        ->members()
        ->whereHas('memberActiveBooking')
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.members.index', ['activeTraining' => 1]))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', MemberResource::collection($activeTrainingMembers))
        ->assertHasProp('activeTraining', true)
        ->assertStatus(200);
});

test('it shows members without active training when filter is off', function () {
    $membersWithoutActiveTraining = User::query()
        ->members()
        ->whereDoesntHave('memberActiveBooking')
        ->orderBy('registration_date', 'DESC')
        ->paginate(10);

    actingAsAdmin()
        ->get(route('admin.members.index', ['activeTraining' => 0]))
        ->assertHasComponent('Admin/Members/Index')
        ->assertHasPaginatedResource('members', MemberResource::collection($membersWithoutActiveTraining))
        ->assertHasProp('activeTraining', false)
        ->assertStatus(200);
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

    $this->assertDatabaseHas(User::class, [
        'id' => $member->id,
        'name' => 'Updated Name',
        'email' => 'updated@liftstation.fitness',
        'gender' => Gender::Female->value,
        'weight' => 65,
        'height' => 170,
    ]);
});

test('it validates member update', function () {
    $member = User::query()->members()->first();

    $invalidData = [
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
    ];

    actingAsAdmin()
        ->put(route('admin.members.update', $member), $invalidData)
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

test('it allows updating member with same email', function () {
    $member = User::query()->members()->first();

    $updatedData = [
        'name' => 'Updated Name',
        'email' => $member->email, // Keep same email
        'registration_date' => $member->registration_date,
        'in_house' => $member->in_house,
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

    $this->assertDatabaseHas(User::class, [
        'id' => $member->id,
        'name' => 'Updated Name',
        'email' => $member->email,
    ]);
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

test('it uploads member profile photo', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();
    $photo = UploadedFile::fake()->image('profile.jpg');

    $updatedData = [
        'name' => $member->name,
        'email' => $member->email,
        'registration_date' => $member->registration_date,
        'in_house' => $member->in_house,
        'gender' => $member->gender,
        'weight' => $member->weight,
        'height' => $member->height,
        'birthdate' => $member->birthdate,
        'blood_type' => $member->blood_type,
        'phone_number' => $member->phone_number,
        'instagram_handle' => $member->instagram_handle,
        'address' => $member->address,
        'emergency_contact' => $member->emergency_contact,
        'color' => $member->color,
        'photo' => $photo,
    ];

    actingAsAdmin()
        ->from(route('admin.members.edit', $member))
        ->post(route('admin.members.update', $member), array_merge($updatedData, ['_method' => 'PUT']))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', $member));

    $member->refresh();

    expect($member->profile_photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($member->profile_photo_path);
});

test('it removes member profile photo', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();

    // First upload a photo
    $photo = UploadedFile::fake()->image('profile.jpg');
    $member->updateProfilePhoto($photo);
    $member->refresh();

    expect($member->profile_photo_path)->not->toBeNull();
    $photoPath = $member->profile_photo_path;
    Storage::disk('public')->assertExists($photoPath);

    // Now remove the photo
    $updatedData = [
        'name' => $member->name,
        'email' => $member->email,
        'registration_date' => $member->registration_date,
        'in_house' => $member->in_house,
        'gender' => $member->gender,
        'weight' => $member->weight,
        'height' => $member->height,
        'birthdate' => $member->birthdate,
        'blood_type' => $member->blood_type,
        'phone_number' => $member->phone_number,
        'instagram_handle' => $member->instagram_handle,
        'address' => $member->address,
        'emergency_contact' => $member->emergency_contact,
        'color' => $member->color,
        'remove_photo' => true,
    ];

    actingAsAdmin()
        ->from(route('admin.members.edit', $member))
        ->post(route('admin.members.update', $member), array_merge($updatedData, ['_method' => 'PUT']))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', $member));

    $member->refresh();

    expect($member->profile_photo_path)->toBeNull();
    Storage::disk('public')->assertMissing($photoPath);
});
