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

// Polling status NIB & Sertifikat Halal dari Urusin Secara Online — lihat
// app/Console/Commands/SyncUrusinSubmissions.php & .agent/workflows/data-entry-integrasi.md §5.4.
Schedule::call(function () {
    Artisan::call('urusin:sync');
})->everyThirtyMinutes()->name('urusin:sync')->withoutOverlapping();

// Modul WRGROUP Super Apps — kirim event outbox (retry + backoff; tetap jalan bila queue
// worker mati), heartbeat status koneksi, dan laporan nihil triwulan. Semua no-op bila
// WRGROUP_ENABLED=false. Lihat .agent/workflows/wrgroup-integrasi.md.
Schedule::call(function () {
    Artisan::call('wrgroup:backfill');
})->dailyAt('02:30')->name('wrgroup:backfill')->withoutOverlapping();
Schedule::call(function () {
    Artisan::call('wrgroup:process');
})->everyMinute()->name('wrgroup:process')->withoutOverlapping();
Schedule::call(function () {
    Artisan::call('wrgroup:heartbeat');
})->everyFiveMinutes()->name('wrgroup:heartbeat')->withoutOverlapping();
Schedule::call(function () {
    Artisan::call('wrgroup:nihil');
})->dailyAt('03:00')->name('wrgroup:nihil')->withoutOverlapping();
