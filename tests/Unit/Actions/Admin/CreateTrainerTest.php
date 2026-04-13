<?php

use App\Actions\Admin\CreateTrainer;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

function trainerAttributes(array $overrides = []): array
{
    return array_merge([
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'registration_date' => now()->format('Y-m-d'),
        'phone_number' => fake()->phoneNumber(),
    ], $overrides);
}

test('it creates a trainer with Role::Trainer and a hashed password', function () {
    $result = app(CreateTrainer::class)->handle(trainerAttributes());

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->role)->toBe(Role::Trainer);

    expect(Hash::check('password', $result->password))->toBeTrue();
    $this->assertDatabaseHas(User::class, ['id' => $result->id, 'role' => Role::Trainer->value]);
});

test('it uploads a profile photo when provided', function () {
    Storage::fake('public');

    $result = app(CreateTrainer::class)->handle(trainerAttributes([
        'photo' => UploadedFile::fake()->image('photo.jpg'),
    ]));

    expect($result->profile_photo_path)->not->toBeNull();
});

test('it skips photo upload when no photo is provided', function () {
    $result = app(CreateTrainer::class)->handle(trainerAttributes());

    expect($result->profile_photo_path)->toBeNull();
});

test('it returns the created User instance', function () {
    $result = app(CreateTrainer::class)->handle(trainerAttributes(['name' => 'John Trainer']));

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->name)->toBe('John Trainer');
});
