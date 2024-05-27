<?php

namespace App\Enums;

enum BloodType: string
{
    case APlus = 'A+';
    case AMinus = 'A-';
    case BPlus = 'B+';
    case BMinus = 'B-';
    case ABPlus = 'AB+';
    case ABMinus = 'AB-';
    case OPlus = 'O+';
    case OMinus = 'O-';

    public static function values(): array
    {
        return collect(self::cases())->map(fn ($case) => $case->value)->toArray();
    }
}
