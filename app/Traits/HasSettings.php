<?php

namespace App\Traits;

trait HasSettings
{
    /**
     * Get user settings or a specific setting value using dot notation
     *
     * @param  string|null  $key  Dot notation key (e.g., 'calendar.default_trainer_id')
     * @param  mixed  $default  Default value if key doesn't exist
     *
     * @example
     *   // Get all settings with defaults merged
     *   $settings = $user->getSetting();
     *
     *   // Get specific setting with dot notation
     *   $trainerId = $user->getSetting('calendar.default_trainer_id');
     *   $startDay = $user->getSetting('calendar.start_day', 'monday');
     *
     *   // Get nested calendar settings
     *   $calendarSettings = $user->getSetting('calendar');
     */
    public function getSetting(?string $key = null, mixed $default = null): mixed
    {
        // Convert role to string if it's an enum
        $role = is_string($this->role) ? $this->role : $this->role->value;

        // Merge defaults with saved settings
        $settings = array_replace_recursive(
            static::getDefaultSettings($role),
            $this->settings ?? []
        );

        if ($key === null) {
            return $settings;
        }

        return data_get($settings, $key, $default);
    }

    /**
     * Update a specific setting using dot notation
     *
     * @param  string  $key  Dot notation key
     *
     * @example
     *   // Set a single calendar setting
     *   $user->setSetting('calendar.start_hour', 8);
     *
     *   // Set a notification preference
     *   $user->setSetting('notifications.new_member_email_to_owners', false);
     *
     *   // Set nested value
     *   $user->setSetting('calendar.default_trainer_id', 5);
     */
    public function setSetting(string $key, mixed $value): bool
    {
        $settings = $this->settings ?? [];

        // Use Laravel's data_set for dot notation support
        data_set($settings, $key, $value);

        return $this->update(['settings' => $settings]);
    }

    /**
     * Update multiple settings at once (merges deeply)
     *
     *
     * @example
     *   // Update multiple calendar settings at once
     *   $user->updateSettings([
     *       'calendar' => [
     *           'start_hour' => 7,
     *           'start_period' => 'AM',
     *           'end_hour' => 9,
     *           'end_period' => 'PM',
     *       ]
     *   ]);
     *
     *   // Update notification preferences
     *   $user->updateSettings([
     *       'notifications' => [
     *           'new_member_email_to_owners' => true,
     *           'owner_emails' => 'admin@gym.com,owner@gym.com',
     *       ]
     *   ]);
     *
     *   // Update across different sections
     *   $user->updateSettings([
     *       'calendar' => ['default_trainer_id' => 3],
     *       'notifications' => ['new_member_email_to_owners' => false],
     *   ]);
     */
    public function updateSettings(array $settings): bool
    {
        $currentSettings = $this->settings ?? [];

        // Deep merge to preserve nested keys
        $mergedSettings = array_replace_recursive($currentSettings, $settings);

        return $this->update(['settings' => $mergedSettings]);
    }

    /**
     * Get default settings structure based on user role
     */
    public static function getDefaultSettings(string $role): array
    {
        return match ($role) {
            'admin' => [
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
                    'owner_emails' => config('mail.owners_emails', ''),
                ],
            ],
            'trainer' => [
                // Future: trainer-specific settings
            ],
            'member' => [
                // Future: member-specific settings
            ],
            default => [],
        };
    }
}
