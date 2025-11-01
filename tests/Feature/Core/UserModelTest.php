<?php

use App\Enums\Role;
use App\Models\User;

describe('members scope', function () {
    it('returns only users with member role', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);
        User::factory()->count(1)->create(['role' => Role::Trainer->value]);

        $results = User::query()->members()->get();

        expect($results)->toHaveCount(2)
            ->and($results->every(fn ($user) => $user->role === Role::Member->value))->toBeTrue();
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

        expect($results)->toHaveCount(3)
            ->and($results->every(fn ($user) => $user->role === Role::Trainer->value))->toBeTrue();
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

        expect($results)->toHaveCount(2)
            ->and($results->every(fn ($user) => $user->role === Role::Member->value))->toBeTrue();
    });

    it('returns users by trainer role', function () {
        User::factory()->count(1)->create(['role' => Role::Member->value]);
        User::factory()->count(3)->create(['role' => Role::Trainer->value]);

        $results = User::query()->byRole(Role::Trainer->value)->get();

        expect($results)->toHaveCount(3)
            ->and($results->every(fn ($user) => $user->role === Role::Trainer->value))->toBeTrue();
    });

    it('returns all users if role is unknown', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);
        User::factory()->count(3)->create(['role' => Role::Trainer->value]);

        $results = User::query()->byRole('unknown')->get();

        expect($results)->toHaveCount(5);
    });

    it('returns all users when role is admin', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);
        User::factory()->count(3)->create(['role' => Role::Trainer->value]);
        User::factory()->count(1)->create(['role' => Role::Admin->value]);

        $results = User::query()->byRole(Role::Admin->value)->get();

        expect($results)->toHaveCount(6);
    });

    it('returns all users when role is empty string', function () {
        User::factory()->count(2)->create(['role' => Role::Member->value]);
        User::factory()->count(3)->create(['role' => Role::Trainer->value]);

        $results = User::query()->byRole('')->get();

        expect($results)->toHaveCount(5);
    });
});

describe('age accessor', function () {
    it('returns null when birthdate is null', function () {
        $user = User::factory()->create(['birthdate' => null]);

        expect($user->age)->toBeNull();
    });

    it('calculates correct age for a known birthdate', function () {
        // Person born on Jan 1, 1990 - should be 35 years old in 2025
        $user = User::factory()->create(['birthdate' => '1990-01-01']);

        expect($user->age)->toBe('35');
    });

    it('returns age 0 for users born this year', function () {
        $user = User::factory()->create(['birthdate' => now()->format('Y-m-d')]);

        expect($user->age)->toBe('0');
    });

    it('calculates age correctly for birthday not yet reached this year', function () {
        // Person born on Dec 31, 1990 - depends on current date
        $user = User::factory()->create(['birthdate' => '1990-12-31']);

        // Age should be either 34 or 35 depending on if we've passed Dec 31
        expect($user->age)->toBeIn(['34', '35']);
    });
});

describe('since accessor', function () {
    it('formats registration date correctly', function () {
        $user = User::factory()->create(['registration_date' => '2025-01-15']);

        expect($user->since)->toBe('Jan 15, 2025');
    });

    it('formats registration date with single digit day', function () {
        $user = User::factory()->create(['registration_date' => '2024-03-05']);

        expect($user->since)->toBe('Mar 5, 2024');
    });

    it('formats registration date for different months', function () {
        $user = User::factory()->create(['registration_date' => '2023-12-25']);

        expect($user->since)->toBe('Dec 25, 2023');
    });
});
