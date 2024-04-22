<?php

use App\Models\User;

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response =$this->actingAs($user)
        ->put('/user/profile-information', [
            'name' => 'Test Name',
            'email' => 'test@example.com',
            'height' => 180,
            'weight' => 72,
            'birthdate' => '2001-04-10',
            'registration_date' => '2024-02-25',
        ]);

    $response->assertSessionHasNoErrors();

    // Refresh the user from the database
    $user->fresh();

    // Assert each field is correctly updated
    expect($user->name)->toEqual('Test Name');
    expect($user->email)->toEqual('test@example.com');
    expect($user->height)->toEqual(180);
    expect($user->weight)->toEqual(72);
    expect($user->birthdate)->toEqual('2001-04-10');
    expect($user->registration_date)->toEqual('2024-02-25');
});
