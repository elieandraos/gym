<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Trainer = 'trainer';
    case Member = 'member';
}
