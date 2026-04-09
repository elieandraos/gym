<?php

namespace App\Actions\Admin;

use App\Mail\Owner\NewMemberEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendNewMemberEmailToOwner
{
    public function handle(User $admin, User $member): void
    {
        $sendEmailToOwners = $admin->getSetting('notifications.new_member_email_to_owners', true);

        if (! $sendEmailToOwners) {
            return;
        }

        $ownersEmails = $admin->getSetting('notifications.owner_emails', config('mail.owners_emails'));

        if (! $ownersEmails) {
            return;
        }

        $emails = array_map('trim', explode(',', $ownersEmails));

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->queue(new NewMemberEmail($member));
            }
        }
    }
}
