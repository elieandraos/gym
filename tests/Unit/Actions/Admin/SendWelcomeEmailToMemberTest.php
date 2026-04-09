<?php

use App\Actions\Admin\SendWelcomeEmailToMember;
use App\Mail\Member\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('it queues a welcome email to the member', function () {
    Mail::fake();

    $member = User::factory()->member()->create();

    app(SendWelcomeEmailToMember::class)->handle($member);

    Mail::assertQueued(WelcomeEmail::class, function (WelcomeEmail $mail) use ($member) {
        return $mail->hasTo($member->email);
    });
});
