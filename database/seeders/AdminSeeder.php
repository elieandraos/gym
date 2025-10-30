<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Owner',
            'email' => 'owner@liftstation.fitness',
            'role' => Role::Admin,
            'settings' => User::getDefaultSettings(Role::Admin->value),
        ]);
    }
}
