<?php

namespace App\Enums;

enum Gender: string
{
    case Female = 'Female';
    case Male = 'Male';

    public static function values(): array
    {
        return collect(self::cases())->map(fn($case) => $case->value)->toArray();
    }

}
