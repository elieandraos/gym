<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteMember
{
    public function handle(User $user): void
    {
        Storage::disk('public')->deleteDirectory("body-compositions/$user->id");
        Storage::disk('public')->deleteDirectory("profile-photos/$user->id");

        DB::transaction(function () use ($user) {
            $user->delete();
        });
    }
}
