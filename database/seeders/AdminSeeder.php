<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'Owner',
            'email' => 'owner@lift-station.fitness',
            'password' => Hash::make('password'),
            'role' => Role::Admin,
            'settings' => User::getDefaultSettings(Role::Admin->value),
        ]);
    }
}
