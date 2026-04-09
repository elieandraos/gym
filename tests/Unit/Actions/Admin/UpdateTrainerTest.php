<?php

use App\Actions\Admin\UpdateTrainer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('it updates the trainer profile fields in the database', function () {
    $user = User::factory()->trainer()->create(['name' => 'Original Name']);

    app(UpdateTrainer::class)->handle($user, ['name' => 'Updated Name']);

    $this->assertDatabaseHas(User::class, ['id' => $user->id, 'name' => 'Updated Name']);
});

test('it uploads a new profile photo when provided', function () {
    Storage::fake('public');

    $user = User::factory()->trainer()->create();

    $result = app(UpdateTrainer::class)->handle($user, [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
    ]);

    expect($result->profile_photo_path)->not->toBeNull();
});

test('it removes the profile photo when remove_photo is true', function () {
    Storage::fake('public');

    $user = User::factory()->trainer()->create();
    $user->updateProfilePhoto(UploadedFile::fake()->image('photo.jpg'));

    $result = app(UpdateTrainer::class)->handle($user, ['remove_photo' => true]);

    expect($result->profile_photo_path)->toBeNull();
});

test('it leaves the existing photo unchanged when neither photo nor remove_photo is present', function () {
    Storage::fake('public');

    $user = User::factory()->trainer()->create();
    $user->updateProfilePhoto(UploadedFile::fake()->image('photo.jpg'));
    $originalPath = $user->fresh()->profile_photo_path;

    $result = app(UpdateTrainer::class)->handle($user, ['name' => 'New Name']);

    expect($result->profile_photo_path)->toBe($originalPath);
});

test('it returns the updated User instance', function () {
    $user = User::factory()->trainer()->create(['name' => 'Before']);

    $result = app(UpdateTrainer::class)->handle($user, ['name' => 'After']);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->name)->toBe('After');
});
