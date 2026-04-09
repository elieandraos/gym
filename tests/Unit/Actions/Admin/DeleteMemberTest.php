<?php

use App\Actions\Admin\DeleteMember;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('it deletes the user record from the database', function () {
    $user = User::factory()->member()->create();

    app(DeleteMember::class)->handle($user);

    $this->assertDatabaseMissing(User::class, ['id' => $user->id]);
});

test('it deletes the body compositions storage directory', function () {
    Storage::fake('public');

    $user = User::factory()->member()->create();
    Storage::disk('public')->put("body-compositions/{$user->id}/photo.jpg", 'content');

    app(DeleteMember::class)->handle($user);

    Storage::disk('public')->assertMissing("body-compositions/{$user->id}/photo.jpg");
});

test('it deletes the profile photos storage directory', function () {
    Storage::fake('public');

    $user = User::factory()->member()->create();
    Storage::disk('public')->put("profile-photos/{$user->id}/photo.jpg", 'content');

    app(DeleteMember::class)->handle($user);

    Storage::disk('public')->assertMissing("profile-photos/{$user->id}/photo.jpg");
});
