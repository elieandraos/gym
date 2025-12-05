<?php

use App\Console\Commands\MarkBookingSlotsComplete;
use App\Console\Commands\SendBookingSlotReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::command(MarkBookingSlotsComplete::class)
    ->everyThirtyMinutes()
    ->timezone(env('APP_TIMEZONE', 'Asia/Beirut'));

Schedule::command(SendBookingSlotReminders::class)
    ->dailyAt('21:00')
    ->timezone(env('APP_TIMEZONE', 'Asia/Beirut'));
