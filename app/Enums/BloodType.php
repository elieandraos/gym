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
}
