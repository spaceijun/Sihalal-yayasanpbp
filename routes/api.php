<?php

use App\Http\Controllers\Api\DataLapanganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:web', 'role:superadmin'])->group(function () {

    Route::prefix('superadmin')->name('api.superadmin.')->group(function () {

        // Data Lapangan API
        Route::prefix('data-lapangans')->name('data-lapangans.')->group(function () {
            Route::get('/', [DataLapanganController::class, 'apiIndex'])
                ->name('index');
            Route::get('/{id}', [DataLapanganController::class, 'apiShow'])
                ->name('show');
            Route::post('/', [DataLapanganController::class, 'apiStore'])
                ->name('store');
            Route::put('/{id}', [DataLapanganController::class, 'apiUpdate'])
                ->name('update');
            Route::delete('/{id}', [DataLapanganController::class, 'apiDestroy'])
                ->name('destroy');

            // Bulk actions
            Route::post('/bulk-delete', [DataLapanganController::class, 'apiBulkDelete'])
                ->name('bulk-delete');

            // Status updates
            Route::post('/{id}/update-status', [DataLapanganController::class, 'apiUpdateStatus'])
                ->name('update-status');
            Route::post('/{id}/update-status-payment', [DataLapanganController::class, 'apiUpdateStatusPayment'])
                ->name('update-status-payment');
            Route::post('/{id}/update-keterangan', [DataLapanganController::class, 'apiUpdateKeterangan'])
                ->name('update-keterangan');

            // File operations
            Route::post('/{id}/upload-file', [DataLapanganController::class, 'apiUploadFile'])
                ->name('upload-file');
            Route::delete('/{id}/delete-file', [DataLapanganController::class, 'apiDeleteFile'])
                ->name('delete-file');
        });

        // // Cashflow API
        // Route::prefix('cashflows')->name('cashflows.')->group(function () {
        //     Route::get('/data', [CashflowController::class, 'getData'])
        //         ->name('data');
        // });

        // // Device API (WA Gateway)
        // Route::prefix('devices')->name('devices.')->group(function () {
        //     Route::post('/status', [DeviceController::class, 'checkDeviceStatus'])
        //         ->name('status');
        //     Route::post('/activate', [DeviceController::class, 'activateDevice'])
        //         ->name('activate');
        //     Route::post('/disconnect', [DeviceController::class, 'disconnect'])
        //         ->name('disconnect');
        // });
    });
});
