<?php

use App\Console\Commands\MarkBookingSlotsComplete;
use App\Console\Commands\SendBookingSlotReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(MarkBookingSlotsComplete::class)->hourly();
Schedule::command(SendBookingSlotReminders::class)->dailyAt('21:00');
