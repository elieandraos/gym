<?php

namespace App\Actions\Admin;

use App\Models\BodyComposition;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateBodyComposition
{
    public function handle(User $user, array $attributes): BodyComposition
    {
        return DB::transaction(function () use ($user, $attributes) {
            /** @var UploadedFile $photo */
            $photo = $attributes['photo'];
            $filename = time().'_'.substr(md5(uniqid()), 0, 8).'.'.$photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs("body-compositions/$user->id", $filename, 'public');

            return BodyComposition::query()->create([
                'user_id' => $user->id,
                'photo_path' => $photoPath,
                'taken_at' => $attributes['taken_at'],
            ]);
        });
    }
}
