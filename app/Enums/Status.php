<?php

namespace App\Enums;

enum Status: string
{
    case Upcoming = 'upcoming';
    case Complete = 'complete';
    case Cancelled = 'cancelled';
    case Frozen = 'Frozen';
}
