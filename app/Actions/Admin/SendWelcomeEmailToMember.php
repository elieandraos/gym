<?php

namespace App\Actions\Admin;

use App\Mail\Member\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailToMember
{
    public function handle(User $member): void
    {
        Mail::to($member->email)->queue(new WelcomeEmail($member));
    }
}
