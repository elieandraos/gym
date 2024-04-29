<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'Admin';
    case Trainer = 'Trainer';
    case Member = 'Member';
}
