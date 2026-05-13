<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('data-lapangan:clean-locks')->everyFiveMinutes();
// Schedule::command('enumerator:check-activity')->monthlyOn(25, '00:00');
Schedule::command('status:update-pembayaran')->daily();
