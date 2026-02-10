<?php

use App\Http\Controllers\Api\DataLapanganController;
use App\Http\Controllers\Api\EnumeratorApi;
use App\Http\Controllers\Api\KoorDataLapanganController;
use App\Http\Controllers\Api\RecruitmentApi;
use App\Models\DataLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/check-nik', [DataLapanganController::class, 'checkNik'])->name('check.nik');
Route::get('data-lapangan/by-enumerator/{enumeratorId}', function ($enumeratorId) {
    $dataLapangan = DataLapangan::where('enumerator_id', $enumeratorId)
        ->whereDoesntHave('spotchecks')
        ->select('id', 'nama_pu')
        ->get();

    return response()->json($dataLapangan);
});
// =============================================
// SUPERADMIN ROUTES
// ============================================
Route::middleware(['auth:web', 'role:superadmin'])->group(function () {
    Route::prefix('superadmin')->name('api.superadmin.')->group(function () {
        // Enumerator API
        Route::get('/enumerators', [EnumeratorApi::class, 'index']);
        Route::delete('/enumerators/{id}', [EnumeratorApi::class, 'destroy']);

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
        });
    });
});

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
