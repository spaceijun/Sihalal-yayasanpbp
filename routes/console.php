<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Menggunakan Schedule::call() + Artisan::call() agar tidak bergantung pada proc_open
// yang sering dinonaktifkan di shared hosting (proc_open digunakan oleh Symfony Process
// ketika menggunakan Schedule::command()).

Schedule::call(function () {
    Artisan::call('data-lapangan:clean-locks');
})->everyMinute()->name('data-lapangan:clean-locks')->withoutOverlapping();

// Schedule::call(function () {
//     Artisan::call('enumerator:check-activity');
// })->monthlyOn(25, '00:00')->name('enumerator:check-activity')->withoutOverlapping();

Schedule::call(function () {
    Artisan::call('status:update-pembayaran');
})->everyMinute()->name('status:update-pembayaran')->withoutOverlapping();

Schedule::call(function () {
    Artisan::call('dataentry:expire-revisi');
})->dailyAt('02:00')->name('dataentry:expire-revisi')->withoutOverlapping();
