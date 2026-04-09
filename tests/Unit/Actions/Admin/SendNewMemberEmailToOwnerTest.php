<?php

use App\Actions\Admin\SendNewMemberEmailToOwner;
use App\Mail\Owner\NewMemberEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('it queues a notification email to the owner', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $admin->setSetting('notifications.owner_emails', 'owner@gym.com');
    $member = User::factory()->member()->create();

    app(SendNewMemberEmailToOwner::class)->handle($admin, $member);

    Mail::assertQueued(NewMemberEmail::class, function (NewMemberEmail $mail) {
        return $mail->hasTo('owner@gym.com');
    });
});

test('it queues notifications to multiple comma-separated owner emails', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $admin->setSetting('notifications.owner_emails', 'owner1@gym.com , owner2@gym.com');
    $member = User::factory()->member()->create();

    app(SendNewMemberEmailToOwner::class)->handle($admin, $member);

    Mail::assertQueued(NewMemberEmail::class, 2);
});

test('it does not queue when the setting is disabled', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $admin->setSetting('notifications.new_member_email_to_owners', false);
    $member = User::factory()->member()->create();

    app(SendNewMemberEmailToOwner::class)->handle($admin, $member);

    Mail::assertNotQueued(NewMemberEmail::class);
});

test('it skips invalid email addresses', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $admin->setSetting('notifications.owner_emails', 'not-an-email , owner@gym.com');
    $member = User::factory()->member()->create();

    app(SendNewMemberEmailToOwner::class)->handle($admin, $member);

    Mail::assertQueued(NewMemberEmail::class, 1);
});
