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

    public static function values(): array
    {
        return collect(self::cases())->map(fn ($case) => $case->value)->toArray();
    }
}
