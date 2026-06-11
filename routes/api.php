<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Enumerator\CashflowEnumeratorController;
use App\Http\Controllers\Api\Enumerator\DataLapanganEnumController;
use App\Http\Controllers\Api\Enumerator\EnumeratorController;
use App\Http\Controllers\Api\Enumerator\HomeApiController;
use App\Http\Controllers\Api\Enumerator\PengumumanEnumController;
use App\Http\Controllers\Api\Enumerator\TicketController as EnumeratorTicketController;
use App\Http\Controllers\Api\FcmController;
use App\Http\Controllers\Api\RankingPendampingApiController;
use App\Http\Controllers\Api\RingkasanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Sihalal Yayasan PBP
|--------------------------------------------------------------------------
|
| File ini hanya berisi endpoint yang digunakan oleh aplikasi Flutter:
|   1. Auth  → /api/login  (login enumerator/user)
|   2. Enumerator (sanctum) → semua endpoint profil, data lapangan, cashflow, pengumuman
|
| Seluruh endpoint berbasis web-session (superadmin, koordinator, data-entry)
| telah dipindahkan ke routes/web.php menggunakan Yajra DataTables.
|
*/

// ──────────────────────────────────────────────────────────
// PUBLIC ENDPOINTS (tidak butuh auth)
// ──────────────────────────────────────────────────────────

/** Login – digunakan oleh aplikasi Flutter */
Route::post('/login', [LoginController::class, 'store']);

/** Ringkasan publik */
Route::get('/ringkasan', [RingkasanController::class, 'index'])->name('api.ringkasan');

/** Ranking pendamping publik */
Route::get('ranking-pendamping', [RankingPendampingApiController::class, 'index'])->name('api.ranking-pendamping');

// ──────────────────────────────────────────────────────────
// ENUMERATOR ROUTES (sanctum)
// ──────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'role:enumerator'])->group(function () {
    Route::prefix('enumerator')->name('api.enumerator.')->group(function () {

        Route::get('dashboard', [HomeApiController::class, 'index'])->name('dashboard');

        // FCM Token
        Route::post('/fcm-token', [FcmController::class, 'store']);

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [EnumeratorController::class, 'index'])->name('index');
            Route::post('/', [EnumeratorController::class, 'store'])->name('store');
            Route::get('/{id}', [EnumeratorController::class, 'show'])->name('show');
            Route::put('/{id}', [EnumeratorController::class, 'update'])->name('update');
            Route::patch('/{id}', [EnumeratorController::class, 'update'])->name('update.patch');
            Route::delete('/{id}', [EnumeratorController::class, 'destroy'])->name('destroy');

            // Bank
            Route::get('/{id}/bank', [EnumeratorController::class, 'getBank'])->name('getBank');
            Route::post('/{id}/bank', [EnumeratorController::class, 'saveBank'])->name('saveBank');
        });

        // Data Lapangan
        Route::prefix('data-lapangan')->name('data-lapangan.')->group(function () {
            Route::get('/', [DataLapanganEnumController::class, 'index'])->name('index');
            Route::get('/{id}', [DataLapanganEnumController::class, 'show'])->name('show');
            Route::delete('/{id}', [DataLapanganEnumController::class, 'destroy'])->name('destroy');
            Route::put('/{id}', [DataLapanganEnumController::class, 'update'])->name('update');
            Route::patch('/{id}', [DataLapanganEnumController::class, 'update'])->name('update.patch');
        });

        Route::middleware(['enumerator.active'])
            ->prefix('data-lapangan')->name('data-lapangan.')->group(function () {
                Route::post('/', [DataLapanganEnumController::class, 'store'])->name('store');
            });

        // Cashflow
        Route::prefix('cashflow')->name('cashflow.')->group(function () {
            Route::get('/', [CashflowEnumeratorController::class, 'index'])->name('index');
            Route::get('/{id}', [CashflowEnumeratorController::class, 'show'])->name('show');
        });

        // Pengumuman
        Route::get('/pengumuman', [PengumumanEnumController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/{id}', [PengumumanEnumController::class, 'show'])->name('pengumuman.show');

        // Tiket Keluhan
        Route::prefix('tiket')->name('tiket.')->group(function () {
            Route::get('/',    [EnumeratorTicketController::class, 'index'])->name('index');
            Route::post('/',   [EnumeratorTicketController::class, 'store'])->name('store');
            Route::get('/{id}', [EnumeratorTicketController::class, 'show'])->name('show');
        });
    });
});
