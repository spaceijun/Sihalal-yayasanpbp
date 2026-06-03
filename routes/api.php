<?php

use App\Http\Controllers\Api\AnalisisHalalController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\DatabankController;
use App\Http\Controllers\Api\DataEntryLapanganController;
use App\Http\Controllers\Api\DataEntryProgressController;
use App\Http\Controllers\Api\DataLapanganController;
use App\Http\Controllers\Api\Enumerator\CashflowEnumeratorController;
use App\Http\Controllers\Api\Enumerator\DataLapanganEnumController;
use App\Http\Controllers\Api\Enumerator\EnumeratorController;
use App\Http\Controllers\Api\Enumerator\HomeApiController;
use App\Http\Controllers\Api\Enumerator\PengumumanEnumController;
use App\Http\Controllers\Api\EnumeratorApi;
use App\Http\Controllers\Api\FcmController;
use App\Http\Controllers\Api\KoorDataLapanganController;
use App\Http\Controllers\Api\RankingPendampingApiController;
use App\Http\Controllers\Api\RecruitmentApi;
use App\Http\Controllers\Api\RingkasanController;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/check-nik', [DataLapanganController::class, 'checkNik'])->name('check.nik');
Route::get('data-lapangan/by-enumerator/{enumeratorId}', function ($enumeratorId) {
    $dataLapangan = DataLapangan::query()
        ->where('enumerator_id', '=', $enumeratorId)
        ->whereDoesntHave('spotchecks', function ($query) {}, '>=', 1)
        ->select('id', 'nama_pu')
        ->get();

    return response()->json($dataLapangan);
});

Route::middleware('auth:sanctum')->post(
    '/analisis-halal',
    [AnalisisHalalController::class, 'analyze']
);

Route::middleware('auth:sanctum')->post('/fcm-token', [FcmController::class, 'store']);
Route::apiResource('data-banks', DatabankController::class);
Route::get('ranking-pendamping', [RankingPendampingApiController::class, 'index'])->name('api.ranking-pendamping');
// Public Ringkasan API
Route::get('/ringkasan', [RingkasanController::class, 'index'])->name('api.ringkasan');

// =============================================
// SUPERADMIN ROUTES
// ============================================
Route::middleware(['auth:web', 'role:superadmin'])->group(
    function () {
        Route::prefix('superadmin')->name('api.superadmin.')->group(function () {
            // Enumerator API
            Route::get('/enumerators', [EnumeratorApi::class, 'index']);
            Route::delete('/enumerators/{id}', [EnumeratorApi::class, 'destroy']);
            Route::post('enumerators/{id}/generate-user', [EnumeratorApi::class, 'generateUser']);

            // Recruitment API
            Route::get('/recruitments', [RecruitmentApi::class, 'index']);
            Route::delete('/recruitments/{id}', [RecruitmentApi::class, 'destroy']);

            // Data Lapangan API
            Route::prefix('data-lapangans')->name('data-lapangans.')->group(function () {
                Route::get('/', [DataLapanganController::class, 'apiIndex'])->name('index');
                Route::get('/{id}', [DataLapanganController::class, 'apiShow'])->name('show');
                Route::post('/', [DataLapanganController::class, 'apiStore'])->name('store');
                Route::put('/{id}', [DataLapanganController::class, 'apiUpdate'])->name('update');
                Route::delete('/{id}', [DataLapanganController::class, 'apiDestroy'])->name('destroy');
                Route::post('/bulk-delete', [DataLapanganController::class, 'apiBulkDelete'])->name('bulk-delete');
                Route::post('/{id}/update-status', [DataLapanganController::class, 'apiUpdateStatus'])->name('update-status');
                Route::post('/{id}/update-status-payment', [DataLapanganController::class, 'apiUpdateStatusPayment'])->name('update-status-payment');
                Route::post('/{id}/update-keterangan', [DataLapanganController::class, 'apiUpdateKeterangan'])->name('update-keterangan');
                Route::post('/{id}/upload-file', [DataLapanganController::class, 'apiUploadFile'])->name('upload-file');
                Route::delete('/{id}/delete-file', [DataLapanganController::class, 'apiDeleteFile'])->name('delete-file');
                Route::post('/{id}/force-unlock', [DataLapanganController::class, 'apiForceUnlock'])->name('force-unlock');
            });
        });
    }
);
// ============================================
// KOORDINATOR ROUTES
// ============================================
Route::middleware(['auth:web', 'role:koordinator'])->group(function () {
    Route::prefix('koordinator')->name('api.koordinator.')->group(function () {
        // Enumerator API
        Route::get('/enumerators', [EnumeratorApi::class, 'index']);
        Route::delete('/enumerators/{id}', [EnumeratorApi::class, 'destroy']);

        // Recruitment API
        Route::get('/recruitments', [RecruitmentApi::class, 'index']);
        Route::delete('/recruitments/{id}', [RecruitmentApi::class, 'destroy']);

        // Data Lapangan API
        Route::prefix('data-lapangans')->name('data-lapangans.')->group(function () {
            Route::get('/', [KoorDataLapanganController::class, 'apiIndex'])->name('index');
        });
    });
});

// ============================================
// DATA ENTRY ROUTES
// ============================================
Route::middleware(['auth:web', 'role:data_entry'])->group(function () {
    Route::prefix('data-entry')->name('api.data-entry.')->group(function () {
        // Data Lapangan API
        Route::prefix('data-lapangans')->name('data-lapangans.')->group(function () {
            Route::get('/', [DataEntryLapanganController::class, 'index'])->name('index');
            Route::post('{id}/lock', [DataEntryLapanganController::class, 'lockData']);
            Route::delete('{id}/lock', [DataEntryLapanganController::class, 'unlockData']);
            Route::post('{id}/unlock-beacon', [DataEntryLapanganController::class, 'unlockBeacon']);
        });
        Route::get('progress', [DataEntryProgressController::class, 'index']);
    });
});

// ============================================
// ENUMERATOR ROUTES
// ============================================
Route::middleware(['auth:sanctum', 'role:enumerator'])->group(function () {
    Route::prefix('enumerator')->name('api.enumerator.')->group(function () {
        Route::get('dashboard', [HomeApiController::class, 'index'])->name('dashboard');

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [EnumeratorController::class, 'index'])->name('index');
            Route::post('/', [EnumeratorController::class, 'store'])->name('store');
            Route::get('/{id}', [EnumeratorController::class, 'show'])->name('show');
            Route::put('/{id}', [EnumeratorController::class, 'update'])->name('update');
            Route::patch('/{id}', [EnumeratorController::class, 'update'])->name('update.patch');
            Route::delete('/{id}', [EnumeratorController::class, 'destroy'])->name('destroy');

            // ROute Bank
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
    });
});
