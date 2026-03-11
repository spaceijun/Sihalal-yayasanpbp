<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek aktivitas enumerator setiap hari tengah malam
Schedule::command('enumerator:check-activity')->daily();
Schedule::command('data-lapangan:clean-locks')->everyFiveMinutes();
