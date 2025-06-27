<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('checklist:daily')->dailyAt('08:00')->runInBackground();
Schedule::command('checklist:weekly')->weeklyOn(1, '08:00')->runInBackground();
Schedule::command('checklist:monthly')->monthlyOn(1, '08:00')->runInBackground();
