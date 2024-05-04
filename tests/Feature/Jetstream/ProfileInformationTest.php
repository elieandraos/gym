<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->put('/user/profile-information', [
            'name' => 'Test Name',
            'email' => 'test@example.com',
            'height' => 180,
            'weight' => 72,
            'gender' => 'Female',
            'birthdate' => '2001-04-10',
        ]);

    $response->assertSessionHasNoErrors();

    // Refresh the user from the database
    $user->fresh();

    // Assert each field is correctly updated
    expect($user->name)->toEqual('Test Name');
    expect($user->email)->toEqual('test@example.com');
    expect($user->height)->toEqual(180);
    expect($user->weight)->toEqual(72);
    expect($user->gender)->toEqual('Female');
    expect($user->birthdate)->toEqual(Carbon::parse('2001-04-10'));
});

test('profile information are validated before update', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->actingAs($user)
        ->put('/user/profile-information', [
            'name' => '', // Required field
            'email' => 'invalid-email', // Invalid email format
            'photo' => UploadedFile::fake()->create('document.pdf', 2000), // Invalid photo
            'gender' => 'Unknown', // Invalid gender
            'blood_type' => 'XX', // Invalid blood type
            'birthdate' => 'not-a-date', // Invalid date format
        ]);

    $response->assertSessionHasErrors([
        'name',
        'email',
        'photo',
        'gender',
        'blood_type',
        'birthdate',
    ]);

    // Test email uniqueness
    $response = $this->actingAs($user)
        ->put('/user/profile-information', [
            'name' => 'Test Name',
            'email' => $otherUser->email,
        ]);

    $response->assertSessionHasErrors(['email']);
});
