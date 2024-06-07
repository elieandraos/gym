<?php

use App\Enums\BloodType;
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
            'blood_type' => BloodType::ABPlus->value,
            'phone_number' => '123456',
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
            'name' => '',
            'email' => 'invalid-email',
            'photo' => UploadedFile::fake()->create('document.pdf', 2000), // Invalid photo
            'gender' => 'Unknown',
            'blood_type' => 'XX',
            'birthdate' => 'not-a-date',
            'phone_number' => '',
        ]);

    $response->assertSessionHasErrors([
        'name',
        'email',
        'photo',
        'gender',
        'blood_type',
        'birthdate',
        'blood_type',
        'phone_number',
    ]);

    // Test email uniqueness
    $response = $this->actingAs($user)
        ->put('/user/profile-information', [
            'name' => 'Test Name',
            'email' => $otherUser->email,
        ]);

    $response->assertSessionHasErrors(['email']);
});
