<?php

use App\Mail\Owner\NewMemberEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

test('it queues notification email to gym owner when member is created', function () {
    config(['mail.owners_emails' => 'owner@example.com']);

    $memberData = [
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'registration_date' => '2025-10-21',
        'in_house' => false,
        'gender' => 'Female',
        'weight' => 65,
        'height' => 170,
        'birthdate' => '1992-08-20',
        'blood_type' => 'O+',
        'phone_number' => '+9876543210',
    ];

    actingAsAdmin()
        ->post(route('admin.members.store'), $memberData)
        ->assertRedirect(route('admin.members.index'));

    Mail::assertQueued(NewMemberEmail::class, function ($mail) {
        return $mail->hasTo('owner@example.com');
    });
});

test('it queues notification emails to multiple gym owners', function () {
    config(['mail.owners_emails' => 'owner1@example.com, owner2@example.com, owner3@example.com']);

    $memberData = [
        'name' => 'Bob Johnson',
        'email' => 'bob.johnson@example.com',
        'registration_date' => '2025-10-21',
        'in_house' => true,
        'gender' => 'Male',
        'weight' => 80,
        'height' => 185,
        'birthdate' => '1990-03-10',
        'blood_type' => 'B+',
        'phone_number' => '+1122334455',
    ];

    actingAsAdmin()
        ->post(route('admin.members.store'), $memberData)
        ->assertRedirect(route('admin.members.index'));

    Mail::assertQueued(NewMemberEmail::class, 3);
    Mail::assertQueued(NewMemberEmail::class, fn ($mail) => $mail->hasTo('owner1@example.com'));
    Mail::assertQueued(NewMemberEmail::class, fn ($mail) => $mail->hasTo('owner2@example.com'));
    Mail::assertQueued(NewMemberEmail::class, fn ($mail) => $mail->hasTo('owner3@example.com'));
});

test('no owner notification emails are sent if owners_emails config is not set', function () {
    config(['mail.owners_emails' => null]);

    $memberData = [
        'name' => 'Test Member',
        'email' => 'testmember@example.com',
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
        ->post(route('admin.members.store'), $memberData);

    // No owner notification emails should be queued
    Mail::assertNotQueued(NewMemberEmail::class);
});

test('owner notification email contains member full name', function () {
    $member = User::factory()->create(['name' => 'Charlie Brown']);

    $mailable = new NewMemberEmail($member);

    $mailable->assertSeeInHtml('Charlie Brown');
});

test('owner notification email contains registration date', function () {
    $member = User::factory()->create([
        'registration_date' => '2025-10-21',
    ]);

    $mailable = new NewMemberEmail($member);

    $mailable->assertSeeInHtml('October 21, 2025');
});

test('owner notification email contains view member profile link', function () {
    $member = User::factory()->create();

    $mailable = new NewMemberEmail($member);

    $mailable->assertSeeInHtml(route('admin.members.show', $member));
    $mailable->assertSeeInHtml('View Member Profile');
});

test('owner notification email has correct subject with member name', function () {
    $member = User::factory()->create(['name' => 'David Wilson']);

    $mailable = new NewMemberEmail($member);

    expect($mailable->envelope()->subject)->toBe('🎉 New Member Alert - David Wilson Just Joined!');
});

test('owner notification email contains the signature sign-off', function () {
    $member = User::factory()->create();

    $mailable = new NewMemberEmail($member);

    $mailable->assertSeeInHtml('See you on the floor');
    $mailable->assertSeeInHtml('The LiftStation Team');
});
