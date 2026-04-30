<?php

namespace App\Providers;

use App\Models\CashflowsKoordinator;
use App\Models\DataLapangan;
use App\Observers\CashflowObserver;
use App\Observers\DataLapanganObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        DataLapangan::observe(DataLapanganObserver::class);
        CashflowsKoordinator::observe(CashflowObserver::class);
    }
}
