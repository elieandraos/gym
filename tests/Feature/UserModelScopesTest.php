<?php

use App\Models\User;
use App\Enums\Role;

describe('members scope', function () {
    it('returns only users with member role', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);
        User::factory()->count(1)->create(['role' => Role::Trainer->value]);

        $results = User::query()->members()->get();

        expect($results)->toHaveCount(2);
        expect($results->every(fn ($user) => $user->role === Role::Member->value))->toBeTrue();
    });

    it('returns no users when there are no members', function () {
        User::factory()->count(3)->create(['role' => Role::Trainer->value]);

        $results = User::query()->members()->get();

        expect($results)->toHaveCount(0);
    });
});

describe('trainers scope', function () {
    it('returns only users with trainer role', function () {
        User::factory()->count(3)->create(['role' => Role::Trainer->value]);
        User::factory()->count(1)->create(['role' => Role::Member->value]);

        $results = User::query()->trainers()->get();

        expect($results)->toHaveCount(3);
        expect($results->every(fn ($user) => $user->role === Role::Trainer->value))->toBeTrue();
    });

    it('returns no users when there are no trainers', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);

        $results = User::query()->trainers()->get();

        expect($results)->toHaveCount(0);
    });
});

describe('byRole scope', function () {
    it('returns users by member role', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);
        User::factory()->count(1)->create(['role' => Role::Trainer->value]);

        $results = User::query()->byRole(Role::Member->value)->get();

        expect($results)->toHaveCount(2);
        expect($results->every(fn ($user) => $user->role === Role::Member->value))->toBeTrue();
    });

    it('returns users by trainer role', function () {
        User::factory()->count(1)->create(['role' => Role::Member->value]);
        User::factory()->count(3)->create(['role' => Role::Trainer->value]);

        $results = User::query()->byRole(Role::Trainer->value)->get();

        expect($results)->toHaveCount(3);
        expect($results->every(fn ($user) => $user->role === Role::Trainer->value))->toBeTrue();
    });

    it('returns all users if role is unknown', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);
        User::factory()->count(3)->create(['role' => Role::Trainer->value]);

        $results = User::query()->byRole('unknown')->get();

        expect($results)->toHaveCount(5);
    });
});
