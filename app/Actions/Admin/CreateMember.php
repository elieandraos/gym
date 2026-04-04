<?php

namespace App\Actions\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateMember
{
    public function handle(User $admin, array $attributes): User
    {
        $member = DB::transaction(function () use ($attributes) {
            /** @var User $member */
            $member = User::query()->create(array_merge(
                collect($attributes)->except(['photo', 'remove_photo'])->all(),
                [
                    'password' => Hash::make('password'),
                    'role' => Role::Member->value,
                ]
            ));

            if (isset($attributes['photo']) && $attributes['photo'] instanceof UploadedFile) {
                $member->updateProfilePhoto($attributes['photo']);
            }

            return $member;
        });

        (new SendWelcomeEmailToMember)->handle($member);
        (new SendNewMemberEmailToOwner)->handle($admin, $member);

        return $member;
    }
}
