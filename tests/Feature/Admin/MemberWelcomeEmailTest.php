<?php

use App\Mail\Member\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

test('it queues welcome email to new member when member is created', function () {
    $memberData = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'registration_date' => '2025-10-21',
        'in_house' => true,
        'gender' => 'Male',
        'weight' => 75,
        'height' => 180,
        'birthdate' => '1995-05-15',
        'blood_type' => 'A+',
        'phone_number' => '+1234567890',
    ];

    actingAsAdmin()
        ->post(route('admin.members.store'), $memberData)
        ->assertSessionHasNoErrors();

    $member = User::query()->where('email', 'john.doe@example.com')->first();

    Mail::assertQueued(WelcomeEmail::class, function ($mail) use ($memberData) {
        return $mail->hasTo($memberData['email']);
    });
});

test('welcome email is queued and not sent immediately', function () {
    $memberData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'registration_date' => '2025-10-21',
        'in_house' => true,
        'gender' => 'Male',
        'weight' => 70,
        'height' => 175,
        'birthdate' => '1993-01-01',
        'blood_type' => 'AB+',
        'phone_number' => '+1111111111',
    ];

    actingAsAdmin()
        ->post(route('admin.members.store'), $memberData);

    // Assert emails were queued, not sent immediately
    Mail::assertQueued(WelcomeEmail::class);
    Mail::assertNotSent(WelcomeEmail::class);
});

test('welcome email contains member first name', function () {
    $member = User::factory()->create(['name' => 'Alice Cooper']);

    $mailable = new WelcomeEmail($member);

    $mailable->assertSeeInHtml('Hey Alice');
});

test('welcome email has correct subject', function () {
    $member = User::factory()->create();

    $mailable = new WelcomeEmail($member);

    expect($mailable->envelope()->subject)->toBe('Welcome to Lift Station! 🎉');
});

test('welcome email contains signature tagline', function () {
    $member = User::factory()->create();

    $mailable = new WelcomeEmail($member);

    $mailable->assertSeeInHtml('count reps');
    $mailable->assertSeeInHtml('make every rep count');
});

test('welcome email contains the signature sign-off', function () {
    $member = User::factory()->create();

    $mailable = new WelcomeEmail($member);

    $mailable->assertSeeInHtml('See you on the floor');
    $mailable->assertSeeInHtml('The Lift Station Team');
});
