<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\AnalisisHalalController;
use App\Http\Controllers\Api\Enumerator\CashflowEnumeratorController;
use App\Http\Controllers\Api\Enumerator\DataLapanganEnumController;
use App\Http\Controllers\Api\Enumerator\EnumeratorController;
use App\Http\Controllers\Api\Enumerator\ExportPdfEnumeratorController;
use App\Http\Controllers\Api\Enumerator\IdCardPdfController;
use App\Http\Controllers\Api\Enumerator\SuratTugasPdfController;
use App\Http\Controllers\Api\Enumerator\HomeApiController;
use App\Http\Controllers\Api\Enumerator\PengumumanEnumController;
use App\Http\Controllers\Api\Enumerator\TicketController as EnumeratorTicketController;
use App\Http\Controllers\Api\Enumerator\DataBankEnumeratorController;
use App\Http\Controllers\Api\Enumerator\TarikSaldoEnumController;
use App\Http\Controllers\Api\FcmController;
use App\Http\Controllers\Api\OcrController;
use App\Http\Controllers\Api\RankingPendampingApiController;
use App\Http\Controllers\Api\RingkasanController;
use App\Http\Controllers\Api\WilayahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Sihalal Yayasan PBP
|--------------------------------------------------------------------------
|
| File ini hanya berisi endpoint yang digunakan oleh aplikasi Flutter:
|   1. Auth  → /api/login  (login enumerator/user)
|   2. Data Entry (sanctum) → analisis halal (AI)
|   3. Enumerator (sanctum) → semua endpoint profil, data lapangan, cashflow, pengumuman
|
| Seluruh endpoint berbasis web-session (superadmin, koordinator, data-entry)
| telah dipindahkan ke routes/web.php menggunakan Yajra DataTables.
|
*/

// ──────────────────────────────────────────────────────────
// PUBLIC ENDPOINTS (tidak butuh auth) - WITH RATE LIMITING
// ──────────────────────────────────────────────────────────

/** Login – digunakan oleh aplikasi Flutter (rate limited: 5 attempts per minute) */
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:5,1');

/** Ringkasan publik (rate limited: 60 requests per minute) */
Route::get('/ringkasan', [RingkasanController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('api.ringkasan');

/** Ranking pendamping publik (rate limited: 60 requests per minute) */
Route::get('ranking-pendamping', [RankingPendampingApiController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('api.ranking-pendamping');

// ──────────────────────────────────────────────────────────
// WILAYAH (PUBLIC) - Cascading dropdown Indonesia
// ──────────────────────────────────────────────────────────
Route::prefix('wilayah')->name('api.wilayah.')->group(function () {
    Route::get('/provinces', [WilayahController::class, 'provinces'])->name('provinces');
    Route::get('/regencies', [WilayahController::class, 'regencies'])->name('regencies');
    Route::get('/districts', [WilayahController::class, 'districts'])->name('districts');
    Route::get('/villages', [WilayahController::class, 'villages'])->name('villages');
    Route::get('/kodepos', [WilayahController::class, 'kodePos'])->name('kodepos');
});

// ──────────────────────────────────────────────────────────
// OCR SCAN KTP (PUBLIC) - Using Gemini Flash API
// ──────────────────────────────────────────────────────────
Route::post('/scan-ktp', [OcrController::class, 'scanKtp'])
    ->middleware('throttle:10,1') // 10 scans per minute
    ->name('api.scan-ktp');

// ──────────────────────────────────────────────────────────
// DATA ENTRY ROUTES (sanctum) - WITH RATE LIMITING
// ──────────────────────────────────────────────────────────
Route::middleware([
    'auth:sanctum',
    'role:data_entry',
    'throttle:120,1' // 120 requests per minute per user
])->group(function () {
    Route::prefix('data-entry')->name('api.data-entry.')->group(function () {

        // Analisis Halal (AI) — /api/data-entry/analisis-halal
        Route::post('/analisis-halal', [AnalisisHalalController::class, 'analyze'])->name('analisis-halal');

    });
});

// ──────────────────────────────────────────────────────────
// ENUMERATOR ROUTES (sanctum) - WITH RATE LIMITING
// ──────────────────────────────────────────────────────────
Route::middleware([
    'auth:sanctum',
    'role:enumerator',
    'throttle:120,1', // 120 requests per minute per user
    'maintenance.enumerator.api',
])->group(function () {
    Route::prefix('enumerator')->name('api.enumerator.')->group(function () {

        Route::get('dashboard', [HomeApiController::class, 'index'])->name('dashboard');

        // Export PDF Laporan
        Route::get('/export-pdf', ExportPdfEnumeratorController::class)->name('export-pdf');

        // Download Surat Tugas PDF
        Route::get('/surat-tugas', SuratTugasPdfController::class)->name('surat-tugas');

        // Download ID Card PDF
        Route::get('/id-card', IdCardPdfController::class)->name('id-card');

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

        // Data Bank (Master Bank)
        Route::prefix('data-bank')->name('data-bank.')->group(function () {
            Route::get('/', [DataBankEnumeratorController::class, 'index'])->name('index');
            Route::get('/{id}', [DataBankEnumeratorController::class, 'show'])->name('show');
        });

        // Pengumuman
        Route::get('/pengumuman', [PengumumanEnumController::class, 'index'])->name('pengumuman.index');
        Route::get('/pengumuman/{id}', [PengumumanEnumController::class, 'show'])->name('pengumuman.show');

        // Analisis Halal (AI)
        Route::post('/analisis-halal', [AnalisisHalalController::class, 'analyze'])->name('analisis-halal');

        // Tiket Keluhan
        Route::prefix('tiket')->name('tiket.')->group(function () {
            Route::get('/',    [EnumeratorTicketController::class, 'index'])->name('index');
            Route::post('/',   [EnumeratorTicketController::class, 'store'])->name('store');
            Route::get('/{id}', [EnumeratorTicketController::class, 'show'])->name('show');
        });

        // Tarik Saldo (Pengajuan Pembayaran Mandiri)
        Route::prefix('tarik-saldo')->name('tarik-saldo.')->group(function () {
            // Ringkasan saldo
            Route::get('/summary',      [TarikSaldoEnumController::class, 'summary'])->name('summary');
            // Data yang bisa diajukan (TERBIT SH + TIDAK ADA PENGAJUAN)
            Route::get('/eligible',     [TarikSaldoEnumController::class, 'eligible'])->name('eligible');
            // Riwayat pengajuan (PENGAJUAN / DIBAYAR / DITOLAK)
            Route::get('/riwayat',      [TarikSaldoEnumController::class, 'riwayat'])->name('riwayat');
            // Ajukan semua data yang eligible sekaligus (bulk)
            Route::post('/ajukan-semua',[TarikSaldoEnumController::class, 'ajukanSemua'])->name('ajukan-semua');
            // Ajukan penarikan untuk satu data lapangan (opsional, tetap tersedia)
            Route::post('/{id}',        [TarikSaldoEnumController::class, 'ajukan'])->name('ajukan');
        });
    });
});
