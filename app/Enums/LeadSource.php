<?php

namespace App\Enums;

enum LeadSource: string
{
    case Owner = 'Owner';
    case Trainer = 'Trainer';
    case Member = 'Member';
    case SocialMedia = 'Social Media';
    case WalkIn = 'Walk-In';

    public static function values(): array
    {
        return collect(self::cases())->map(fn ($case) => $case->value)->toArray();
    }
}
