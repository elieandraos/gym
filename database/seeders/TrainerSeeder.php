<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        $colors = ['bg-blue-50', 'bg-amber-100', 'bg-pink-50', 'bg-green-50', 'bg-purple-100', 'bg-yellow-100', 'bg-lime-100', 'bg-red-100'];

        User::factory(3)->sequence(
            ['role' => Role::Trainer, 'color' => $colors[0]],
            ['role' => Role::Trainer, 'color' => $colors[1]],
            ['role' => Role::Trainer, 'color' => $colors[2]],
        )->create();
    }
}
