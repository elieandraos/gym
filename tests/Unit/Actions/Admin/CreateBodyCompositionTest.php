<?php

use App\Actions\Admin\CreateBodyComposition;
use App\Models\BodyComposition;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('it uploads the photo and creates the body composition record', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $takenAt = Carbon::today()->format('Y-m-d');

    $result = (new CreateBodyComposition)->handle($user, [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
        'taken_at' => $takenAt,
    ]);

    expect($result)->toBeInstanceOf(BodyComposition::class)
        ->and($result->photo_path)->toContain("body-compositions/{$user->id}/");

    Storage::disk('public')->assertExists($result->photo_path);
    $this->assertDatabaseHas(BodyComposition::class, ['user_id' => $user->id, 'taken_at' => $takenAt]);
});

test('it generates a filename with the correct extension', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $result = (new CreateBodyComposition)->handle($user, [
        'photo' => UploadedFile::fake()->image('photo.jpg'),
        'taken_at' => Carbon::today()->format('Y-m-d'),
    ]);

    expect($result->photo_path)->toEndWith('.jpg');
});
