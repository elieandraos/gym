<?php

use App\Enums\BloodType;
use App\Enums\Gender;
use App\Http\Resources\MemberResource;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
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
    $this->get(route('admin.member-created', $member))->assertRedirect(route('login'));
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

    $this->assertDatabaseHas(User::class, $data);

    $member = User::query()->where('email', 'elie@liftstation.fitness')->first();
    $response->assertRedirect(route('admin.member-created', $member));
});

test('it shows member creation celebration page', function () {
    $member = User::query()->members()->first();

    actingAsAdmin()
        ->get(route('admin.member-created', $member))
        ->assertHasComponent('Admin/MemberCreated/Index')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);
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

test('it shows member information', function () {
    $member = User::query()->members()->first();
    $member->load([
        'memberActiveBooking.bookingSlots' => function ($query) {
            $query->orderBy('start_time')
                ->with(['bookingSlotWorkouts.workout']);
        },
        'memberScheduledBookings',
        'lastBodyComposition',
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

    expect($member->profile_photo_path)->not->toBeNull()
        ->and($member->profile_photo_path)->toContain("profile-photos/{$member->id}/");
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

test('owner notification email is sent when setting is enabled', function () {
    Mail::fake();

    // Create admin with email notification enabled and custom email
    $admin = \App\Models\User::factory()->create([
        'role' => \App\Enums\Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
            'notifications' => [
                'new_member_email_to_owners' => true,
                'owner_emails' => 'custom@example.com',
            ],
        ],
    ]);

    $memberData = [
        'name' => 'Test Member',
        'email' => 'test@example.com',
        'registration_date' => '2025-10-21',
        'in_house' => true,
        'gender' => \App\Enums\Gender::Male->value,
        'weight' => 75,
        'height' => 180,
        'birthdate' => '1990-01-01',
        'blood_type' => \App\Enums\BloodType::OPlus->value,
        'phone_number' => '+1234567890',
        'instagram_handle' => 'test',
        'address' => 'test address',
        'emergency_contact' => 'test contact',
        'color' => 'bg-blue-50',
    ];

    test()->actingAs($admin)
        ->post(route('admin.members.store'), $memberData);

    Mail::assertQueued(\App\Mail\Owner\NewMemberEmail::class, function ($mail) {
        return $mail->hasTo('custom@example.com');
    });
});

test('owner notification email is NOT sent when setting is disabled', function () {
    Mail::fake();

    // Create admin with email notification disabled
    $admin = \App\Models\User::factory()->create([
        'role' => \App\Enums\Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
            'notifications' => [
                'new_member_email_to_owners' => false,
                'owner_emails' => 'owner@example.com',
            ],
        ],
    ]);

    $memberData = [
        'name' => 'Test Member',
        'email' => 'test2@example.com',
        'registration_date' => '2025-10-21',
        'in_house' => true,
        'gender' => \App\Enums\Gender::Male->value,
        'weight' => 75,
        'height' => 180,
        'birthdate' => '1990-01-01',
        'blood_type' => \App\Enums\BloodType::OPlus->value,
        'phone_number' => '+1234567890',
        'instagram_handle' => 'test',
        'address' => 'test address',
        'emergency_contact' => 'test contact',
        'color' => 'bg-blue-50',
    ];

    test()->actingAs($admin)
        ->post(route('admin.members.store'), $memberData);

    // No owner notification emails should be sent
    Mail::assertNotQueued(\App\Mail\Owner\NewMemberEmail::class);
});

test('owner notification emails sent to multiple addresses from settings', function () {
    Mail::fake();

    // Create admin with multiple custom emails
    $admin = \App\Models\User::factory()->create([
        'role' => \App\Enums\Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
            'notifications' => [
                'new_member_email_to_owners' => true,
                'owner_emails' => 'owner1@example.com, owner2@example.com, owner3@example.com',
            ],
        ],
    ]);

    $memberData = [
        'name' => 'Test Member',
        'email' => 'test3@example.com',
        'registration_date' => '2025-10-21',
        'in_house' => true,
        'gender' => \App\Enums\Gender::Male->value,
        'weight' => 75,
        'height' => 180,
        'birthdate' => '1990-01-01',
        'blood_type' => \App\Enums\BloodType::OPlus->value,
        'phone_number' => '+1234567890',
        'instagram_handle' => 'test',
        'address' => 'test address',
        'emergency_contact' => 'test contact',
        'color' => 'bg-blue-50',
    ];

    test()->actingAs($admin)
        ->post(route('admin.members.store'), $memberData);

    // Should send to all 3 email addresses
    Mail::assertQueued(\App\Mail\Owner\NewMemberEmail::class, 3);
    Mail::assertQueued(\App\Mail\Owner\NewMemberEmail::class, fn ($mail) => $mail->hasTo('owner1@example.com'));
    Mail::assertQueued(\App\Mail\Owner\NewMemberEmail::class, fn ($mail) => $mail->hasTo('owner2@example.com'));
    Mail::assertQueued(\App\Mail\Owner\NewMemberEmail::class, fn ($mail) => $mail->hasTo('owner3@example.com'));
});

test('settings emails override config emails', function () {
    Mail::fake();
    config(['mail.owners_emails' => 'config@example.com']);

    // Create admin with custom email in settings (should override config)
    $admin = \App\Models\User::factory()->create([
        'role' => \App\Enums\Role::Admin,
        'settings' => [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
            'notifications' => [
                'new_member_email_to_owners' => true,
                'owner_emails' => 'settings@example.com',
            ],
        ],
    ]);

    $memberData = [
        'name' => 'Test Member',
        'email' => 'test4@example.com',
        'registration_date' => '2025-10-21',
        'in_house' => true,
        'gender' => \App\Enums\Gender::Male->value,
        'weight' => 75,
        'height' => 180,
        'birthdate' => '1990-01-01',
        'blood_type' => \App\Enums\BloodType::OPlus->value,
        'phone_number' => '+1234567890',
        'instagram_handle' => 'test',
        'address' => 'test address',
        'emergency_contact' => 'test contact',
        'color' => 'bg-blue-50',
    ];

    test()->actingAs($admin)
        ->post(route('admin.members.store'), $memberData);

    // Should send to settings email, not config email
    Mail::assertQueued(\App\Mail\Owner\NewMemberEmail::class, fn ($mail) => $mail->hasTo('settings@example.com'));
    Mail::assertNotQueued(\App\Mail\Owner\NewMemberEmail::class, fn ($mail) => $mail->hasTo('config@example.com'));
});

test('it renders the delete member confirmation page', function () {
    $member = User::query()->members()->first();

    actingAsAdmin()
        ->get(route('admin.members.delete', $member))
        ->assertHasComponent('Admin/DeleteMember/Index')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);
});

test('it deletes a member and cascades to all related data', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();

    // Create test data: bookings, booking slots, workouts, body compositions
    $booking = $member->memberBookings()->first();
    $bookingSlot = $booking->bookingSlots()->first();
    $bookingSlotWorkout = $bookingSlot->bookingSlotWorkouts()->first();
    $bookingSlotWorkoutSet = $bookingSlotWorkout?->bookingSlotWorkoutSets()->first();

    // Create body composition and storage files
    $bodyComposition = \App\Models\BodyComposition::factory()->create(['user_id' => $member->id]);
    Storage::disk('public')->put("body-compositions/{$member->id}/test.jpg", 'test content');
    Storage::disk('public')->put("profile-photos/{$member->id}/profile.jpg", 'test content');

    // Verify data exists before deletion
    $this->assertDatabaseHas('users', ['id' => $member->id]);
    $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    $this->assertDatabaseHas('booking_slots', ['id' => $bookingSlot->id]);
    if ($bookingSlotWorkout) {
        $this->assertDatabaseHas('booking_slot_workouts', ['id' => $bookingSlotWorkout->id]);
    }
    if ($bookingSlotWorkoutSet) {
        $this->assertDatabaseHas('booking_slot_workout_sets', ['id' => $bookingSlotWorkoutSet->id]);
    }
    $this->assertDatabaseHas('body_compositions', ['id' => $bodyComposition->id]);
    Storage::disk('public')->assertExists("body-compositions/{$member->id}/test.jpg");
    Storage::disk('public')->assertExists("profile-photos/{$member->id}/profile.jpg");

    // Delete the member
    actingAsAdmin()
        ->delete(route('admin.members.destroy', $member))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.index'));

    // Verify CASCADE deletion worked
    $this->assertDatabaseMissing('users', ['id' => $member->id]);
    $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    $this->assertDatabaseMissing('booking_slots', ['id' => $bookingSlot->id]);
    if ($bookingSlotWorkout) {
        $this->assertDatabaseMissing('booking_slot_workouts', ['id' => $bookingSlotWorkout->id]);
    }
    if ($bookingSlotWorkoutSet) {
        $this->assertDatabaseMissing('booking_slot_workout_sets', ['id' => $bookingSlotWorkoutSet->id]);
    }
    $this->assertDatabaseMissing('body_compositions', ['id' => $bodyComposition->id]);

    // Verify storage files were deleted
    Storage::disk('public')->assertMissing("body-compositions/{$member->id}/test.jpg");
    Storage::disk('public')->assertMissing("profile-photos/{$member->id}/profile.jpg");
});

test('member deletion does not affect workouts table', function () {
    $member = User::query()->members()->first();
    $booking = $member->memberBookings()->first();
    $bookingSlot = $booking->bookingSlots()->first();
    $bookingSlotWorkout = $bookingSlot->bookingSlotWorkouts()->first();

    if (! $bookingSlotWorkout) {
        expect(true)->toBeTrue(); // Skip test if no workout exists

        return;
    }

    $workoutId = $bookingSlotWorkout->workout_id;

    // Verify workout exists before member deletion
    $this->assertDatabaseHas('workouts', ['id' => $workoutId]);

    // Delete the member
    actingAsAdmin()
        ->delete(route('admin.members.destroy', $member))
        ->assertSessionHasNoErrors();

    // Verify workout definition still exists (not cascaded)
    $this->assertDatabaseHas('workouts', ['id' => $workoutId]);
});
