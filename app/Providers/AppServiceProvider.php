<?php

namespace App\Providers;

use App\Models\CashflowsKoordinator;
use App\Models\DataLapangan;
use App\Observers\CashflowObserver;
use App\Observers\DataLapanganObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        DataLapangan::observe(DataLapanganObserver::class);
        CashflowsKoordinator::observe(CashflowObserver::class);

        $this->configureRateLimiters();
    }

    /**
     * Konfigurasi custom rate limiter untuk endpoint publik.
     */
    protected function configureRateLimiters(): void
    {
        // Formulir Halal: maks 5 submit per 10 menit per IP
        RateLimiter::for('formulir-halal', function (Request $request) {
            return Limit::perMinutes(10, 5)
                ->by($request->ip())
                ->response(function () {
                    return redirect()->back()
                        ->with('error', 'Terlalu banyak percobaan pengiriman. Silakan tunggu beberapa menit sebelum mencoba lagi.');
                });
        });

        // Upload File Sekuensial: maks 20 upload per 5 menit per IP
        RateLimiter::for('upload-file', function (Request $request) {
            return Limit::perMinutes(5, 20)
                ->by($request->ip());
        });
    }
}
