<?php

namespace App\Providers;

use App\Models\Cashflow;
use App\Models\DataLapangan;
use App\Observers\CashflowObserver;
use App\Observers\DataLapanganObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DataLapangan::observe(DataLapanganObserver::class);
        Cashflow::observe(CashflowObserver::class);
    }
}
