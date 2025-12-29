<?php

use App\Http\Resources\MemberResource;
use App\Models\BodyComposition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    setupUsersAndBookings();
});

test('body composition routes require authentication', function () {
    $member = User::query()->members()->first();

    $this->get(route('admin.members.body-composition.create', $member))->assertRedirect(route('login'));
    $this->post(route('admin.members.body-composition.store', $member))->assertRedirect(route('login'));
});

test('it shows the body composition create form', function () {
    $member = User::query()->members()->first();
    $member->load('memberActiveBooking');

    actingAsAdmin()
        ->get(route('admin.members.body-composition.create', $member))
        ->assertHasComponent('Admin/BodyCompositions/Create')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertHasProp('defaultDate', Carbon::today()->format('Y-m-d'))
        ->assertStatus(200);
});

test('it uploads body composition photo successfully', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();
    $photo = UploadedFile::fake()->image('body-composition.jpg');
    $takenAt = Carbon::today()->format('Y-m-d');

    $data = [
        'photo' => $photo,
        'taken_at' => $takenAt,
    ];

    actingAsAdmin()
        ->post(route('admin.members.body-composition.store', $member), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.members.show', $member));

    $this->assertDatabaseHas(BodyComposition::class, [
        'user_id' => $member->id,
        'taken_at' => $takenAt,
    ]);

    $bodyComposition = BodyComposition::query()->where('user_id', $member->id)->first();
    expect($bodyComposition->photo_path)->toContain("body-compositions/{$member->id}/");
    Storage::disk('public')->assertExists($bodyComposition->photo_path);
});

test('it validates body composition photo is required', function () {
    $member = User::query()->members()->first();

    $data = [
        'taken_at' => Carbon::today()->format('Y-m-d'),
    ];

    actingAsAdmin()
        ->post(route('admin.members.body-composition.store', $member), $data)
        ->assertSessionHasErrors(['photo'])
        ->assertStatus(302);
});

test('it validates body composition photo must be an image', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();
    $invalidFile = UploadedFile::fake()->create('document.pdf', 100);

    $data = [
        'photo' => $invalidFile,
        'taken_at' => Carbon::today()->format('Y-m-d'),
    ];

    actingAsAdmin()
        ->post(route('admin.members.body-composition.store', $member), $data)
        ->assertSessionHasErrors(['photo'])
        ->assertStatus(302);
});

test('it validates taken_at date is required', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();
    $photo = UploadedFile::fake()->image('body-composition.jpg');

    $data = [
        'photo' => $photo,
    ];

    actingAsAdmin()
        ->post(route('admin.members.body-composition.store', $member), $data)
        ->assertSessionHasErrors(['taken_at'])
        ->assertStatus(302);
});

test('it displays latest body composition on member show page', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();
    $photo = UploadedFile::fake()->image('body-composition.jpg');
    $takenAt = Carbon::today()->subDays(5);

    // Create a body composition
    BodyComposition::factory()->create([
        'user_id' => $member->id,
        'photo_path' => $photo->store('body-compositions', 'public'),
        'taken_at' => $takenAt,
    ]);

    $member->load([
        'memberActiveBooking.bookingSlots' => function ($query) {
            $query->orderBy('start_time');
        },
        'memberScheduledBookings',
        'lastBodyComposition',
    ]);

    actingAsAdmin()
        ->get(route('admin.members.show', $member))
        ->assertHasComponent('Admin/Members/Show')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertHasProp('member.last_body_composition.taken_at_formatted', $takenAt->format('D M jS, Y'))
        ->assertStatus(200);
});

test('it displays empty state message when no body composition exists', function () {
    $member = User::query()->members()->first();
    $member->load([
        'memberActiveBooking.bookingSlots' => function ($query) {
            $query->orderBy('start_time');
        },
        'memberScheduledBookings',
        'lastBodyComposition',
    ]);

    actingAsAdmin()
        ->get(route('admin.members.show', $member))
        ->assertHasComponent('Admin/Members/Show')
        ->assertHasResource('member', MemberResource::make($member))
        ->assertStatus(200);

    // The empty state message is handled on the frontend, so we just verify the prop is null
    expect($member->lastBodyComposition)->toBeNull();
});

test('it returns the latest body composition when multiple exist', function () {
    Storage::fake('public');

    $member = User::query()->members()->first();

    // Create multiple body compositions
    BodyComposition::factory()->create([
        'user_id' => $member->id,
        'taken_at' => Carbon::today()->subDays(30),
    ]);

    $latestBodyComposition = BodyComposition::factory()->create([
        'user_id' => $member->id,
        'taken_at' => Carbon::today()->subDays(5),
    ]);

    BodyComposition::factory()->create([
        'user_id' => $member->id,
        'taken_at' => Carbon::today()->subDays(15),
    ]);

    $member->load('lastBodyComposition');

    expect($member->lastBodyComposition->id)->toBe($latestBodyComposition->id);
});
