<?php

use App\Actions\Admin\CreateMember;
use App\Enums\BloodType;
use App\Enums\Gender;
use App\Enums\Role;
use App\Mail\Member\WelcomeEmail;
use App\Mail\Owner\NewMemberEmail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

function memberAttributes(array $overrides = []): array
{
    return array_merge([
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'registration_date' => now()->format('Y-m-d'),
        'gender' => fake()->randomElement(Gender::cases())->value,
        'weight' => 70,
        'height' => 175,
        'birthdate' => '1990-01-01',
        'blood_type' => fake()->randomElement(BloodType::cases())->value,
        'phone_number' => fake()->phoneNumber(),
    ], $overrides);
}

test('it creates a member with Role::Member and a hashed password', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();

    $result = app(CreateMember::class)->handle($admin, memberAttributes());

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->role)->toBe(Role::Member);

    expect(Hash::check('password', $result->password))->toBeTrue();
    $this->assertDatabaseHas(User::class, ['id' => $result->id, 'role' => Role::Member->value]);
});

test('it uploads a profile photo when provided', function () {
    Mail::fake();
    Storage::fake('public');

    $admin = User::factory()->admin()->create();

    $result = app(CreateMember::class)->handle($admin, memberAttributes([
        'photo' => UploadedFile::fake()->image('photo.jpg'),
    ]));

    expect($result->profile_photo_path)->not->toBeNull();
});

test('it skips photo upload when no photo is provided', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();

    $result = app(CreateMember::class)->handle($admin, memberAttributes());

    expect($result->profile_photo_path)->toBeNull();
});

test('it queues a welcome email and owner notification', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $admin->setSetting('notifications.owner_emails', 'owner@gym.com');

    $result = app(CreateMember::class)->handle($admin, memberAttributes());

    Mail::assertQueued(WelcomeEmail::class, function (WelcomeEmail $mail) use ($result) {
        return $mail->hasTo($result->email);
    });

    Mail::assertQueued(NewMemberEmail::class, function (NewMemberEmail $mail) {
        return $mail->hasTo('owner@gym.com');
    });
});

test('it returns the created User instance', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $attributes = memberAttributes(['name' => 'Jane Doe']);

    $result = app(CreateMember::class)->handle($admin, $attributes);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->name)->toBe('Jane Doe');
});
