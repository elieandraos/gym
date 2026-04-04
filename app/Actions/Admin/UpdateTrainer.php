<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateTrainer
{
    public function handle(User $user, array $attributes): User
    {
        return DB::transaction(function () use ($user, $attributes) {
            if (isset($attributes['photo']) && $attributes['photo'] instanceof UploadedFile) {
                $user->updateProfilePhoto($attributes['photo']);
            }

            if (($attributes['remove_photo'] ?? false) === true) {
                $user->deleteProfilePhoto();
            }

            $user->update(
                collect($attributes)->except(['photo', 'remove_photo'])->all()
            );

            return $user;
        });
    }
}
