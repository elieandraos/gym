<?php

use App\Enums\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    // Create trainers for default_trainer_id validation
    User::factory()->count(3)->create(['role' => Role::Trainer]);
});

test('settings routes require authentication', function () {
    $this->get(route('admin.settings.edit'))->assertRedirect(route('login'));
    $this->patch(route('admin.settings.update'))->assertRedirect(route('login'));
});

test('admin can view settings page', function () {
    actingAsAdmin()
        ->get(route('admin.settings.edit'))
        ->assertHasComponent('Admin/Settings/Index')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('trainers')
            ->has('settings')
            ->has('settings.calendar')
            ->has('settings.notifications')
        );
});

test('admin can update calendar settings', function () {
    $trainer = User::query()->trainers()->first();

    $calendarSettings = [
        'calendar' => [
            'default_trainer_id' => $trainer->id,
            'start_day' => 'tuesday',
            'end_day' => 'friday',
            'start_hour' => 7,
            'start_period' => 'AM',
            'end_hour' => 9,
            'end_period' => 'PM',
        ],
    ];

    actingAsAdmin()
        ->patch(route('admin.settings.update'), $calendarSettings)
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHas('flash.banner', 'Settings updated successfully')
        ->assertSessionHas('flash.bannerStyle', 'success');

    $admin = User::query()->where('role', Role::Admin)->latest()->first();

    expect($admin->getSetting('calendar.default_trainer_id'))->toBe($trainer->id)
        ->and($admin->getSetting('calendar.start_day'))->toBe('tuesday')
        ->and($admin->getSetting('calendar.end_day'))->toBe('friday')
        ->and($admin->getSetting('calendar.start_hour'))->toBe(7)
        ->and($admin->getSetting('calendar.start_period'))->toBe('AM')
        ->and($admin->getSetting('calendar.end_hour'))->toBe(9)
        ->and($admin->getSetting('calendar.end_period'))->toBe('PM');
});

test('admin can update notification settings', function () {
    $notificationSettings = [
        'notifications' => [
            'new_member_email_to_owners' => false,
            'owner_emails' => 'owner1@gym.com,owner2@gym.com',
        ],
    ];

    actingAsAdmin()
        ->patch(route('admin.settings.update'), $notificationSettings)
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHas('flash.banner', 'Settings updated successfully');

    $admin = User::query()->where('role', Role::Admin)->latest()->first();

    expect($admin->getSetting('notifications.new_member_email_to_owners'))->toBeFalse()
        ->and($admin->getSetting('notifications.owner_emails'))->toBe('owner1@gym.com,owner2@gym.com');
});

test('validation fails for invalid trainer id', function () {
    actingAsAdmin()
        ->patch(route('admin.settings.update'), [
            'calendar' => [
                'default_trainer_id' => 99999,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ])
        ->assertSessionHasErrors('calendar.default_trainer_id');
});

test('validation fails for invalid day names', function () {
    actingAsAdmin()
        ->patch(route('admin.settings.update'), [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'funday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ])
        ->assertSessionHasErrors('calendar.start_day');

    actingAsAdmin()
        ->patch(route('admin.settings.update'), [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'notaday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ])
        ->assertSessionHasErrors('calendar.end_day');
});

test('validation fails for invalid hours', function () {
    actingAsAdmin()
        ->patch(route('admin.settings.update'), [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 0,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ])
        ->assertSessionHasErrors('calendar.start_hour');

    actingAsAdmin()
        ->patch(route('admin.settings.update'), [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 13,
                'end_period' => 'PM',
            ],
        ])
        ->assertSessionHasErrors('calendar.end_hour');
});

test('validation fails for invalid period', function () {
    actingAsAdmin()
        ->patch(route('admin.settings.update'), [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'MORNING',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
        ])
        ->assertSessionHasErrors('calendar.start_period');
});

test('validation fails when start time is after end time', function () {
    actingAsAdmin()
        ->patch(route('admin.settings.update'), [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 10,
                'start_period' => 'PM',
                'end_hour' => 6,
                'end_period' => 'AM',
            ],
        ])
        ->assertSessionHasErrors('calendar.end_hour');
});

test('validation fails for invalid email format in owner emails', function () {
    actingAsAdmin()
        ->patch(route('admin.settings.update'), [
            'calendar' => [
                'default_trainer_id' => null,
                'start_day' => 'monday',
                'end_day' => 'saturday',
                'start_hour' => 6,
                'start_period' => 'AM',
                'end_hour' => 10,
                'end_period' => 'PM',
            ],
            'notifications' => [
                'new_member_email_to_owners' => true,
                'owner_emails' => 'valid@email.com,invalid-email,another@valid.com',
            ],
        ])
        ->assertSessionHasErrors('notifications.owner_emails');
});

test('settings are merged correctly with existing settings', function () {
    $admin = User::factory()->admin()->create();

    // Update only specific calendar settings (not all fields)
    test()->actingAs($admin)->patch(route('admin.settings.update'), [
        'calendar' => [
            'start_day' => 'wednesday',
            'end_day' => 'sunday',
        ],
    ]);

    $admin = $admin->fresh();

    // Updated calendar settings should change
    expect($admin->getSetting('calendar.start_day'))->toBe('wednesday')
        ->and($admin->getSetting('calendar.end_day'))->toBe('sunday')
        ->and($admin->getSetting('calendar.start_hour'))->toBe(6)
        ->and($admin->getSetting('calendar.start_period'))->toBe('AM')
        ->and($admin->getSetting('notifications.new_member_email_to_owners'))->toBeTrue();
});

test('getSetting returns default value when not set', function () {
    $user = User::factory()->create(['role' => Role::Admin, 'settings' => []]);

    expect($user->getSetting('calendar.start_day'))->toBe('monday')
        ->and($user->getSetting('calendar.end_day'))->toBe('saturday')
        ->and($user->getSetting('notifications.new_member_email_to_owners'))->toBeTrue();
});

test('setSetting updates specific setting with dot notation', function () {
    $user = User::factory()->admin()->create();

    $user->setSetting('calendar.start_hour', 8);

    expect($user->fresh()->getSetting('calendar.start_hour'))->toBe(8)
        ->and($user->fresh()->getSetting('calendar.start_day'))->toBe('monday'); // Other settings unchanged
});

test('updateSettings deep merges settings', function () {
    $user = User::factory()->admin()->create();

    // Initial settings have defaults
    expect($user->getSetting('calendar.start_hour'))->toBe(6);

    // Update only specific keys
    $user->updateSettings([
        'calendar' => [
            'start_hour' => 9,
        ],
    ]);

    // Updated key should change
    expect($user->fresh()->getSetting('calendar.start_hour'))->toBe(9)
        // Other calendar settings should remain
        ->and($user->fresh()->getSetting('calendar.start_day'))->toBe('monday')
        ->and($user->fresh()->getSetting('calendar.end_day'))->toBe('saturday');
});
