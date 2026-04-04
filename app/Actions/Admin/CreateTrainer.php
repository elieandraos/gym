<?php

namespace App\Actions\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateTrainer
{
    public function handle(array $attributes): User
    {
        return DB::transaction(function () use ($attributes) {
            /** @var User $trainer */
            $trainer = User::query()->create(array_merge(
                collect($attributes)->except(['photo', 'remove_photo'])->all(),
                [
                    'password' => Hash::make('password'),
                    'role' => Role::Trainer->value,
                ]
            ));

            if (isset($attributes['photo']) && $attributes['photo'] instanceof UploadedFile) {
                $trainer->updateProfilePhoto($attributes['photo']);
            }

            return $trainer;
        });
    }
}
