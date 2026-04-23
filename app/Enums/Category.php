<?php

namespace App\Enums;

enum Category: string
{
    case Chest = 'Chest';
    case Biceps = 'Biceps';
    case Triceps = 'Triceps';
    case Shoulders = 'Shoulders';
    case Back = 'Back';
    case Legs = 'Legs';
    case Core = 'Core';
    case Glutes = 'Glutes';
    case Abs = 'Abs';
    case HIIT = 'HIIT';
    case Cardio = 'Cardio';
    case Functional = 'Functional';
    case Training = 'Training';
    case Stability = 'Stability';
    case Mobility = 'Mobility';
    case Forearms = 'Forearms';

    /**
     * Get all enum case values as an array of strings.
     *
     * @return string[] Array of enum case values.
     */
    public static function values(): array
    {
        return collect(self::cases())->map(fn ($case) => $case->value)->toArray();
    }
}
