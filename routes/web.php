<?php

use App\Http\Controllers\Koordinator\CashflowKoordinatorController;
use App\Http\Controllers\Koordinator\DashboardController as KoordinatorDashboardController;
use App\Http\Controllers\Koordinator\DataLapanganController as KoordinatorDataLapanganController;
use App\Http\Controllers\Koordinator\DataPendampingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\CashflowController;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\DataLapanganController;
use App\Http\Controllers\Superadmin\DeviceController;
use App\Http\Controllers\Superadmin\EnumeratorController;
use App\Http\Controllers\Superadmin\KoordinatorController;
use App\Http\Controllers\Superadmin\LaporanHarianController;
use App\Http\Controllers\Superadmin\RecruitmentController;
use App\Http\Controllers\Superadmin\SettingwebsiteController;
use App\Http\Controllers\Superadmin\UserController;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('auth.login');
});
Route::get('formulir-halal', [DataLapanganController::class, 'create'])->name('formulir.halal');
Route::post('formulir-halal', [DataLapanganController::class, 'store'])->name('formulir.halal.store');
Route::get('recruitment', [RecruitmentController::class, 'create'])->name('recruitment.formulir');
Route::post('recruitment', [RecruitmentController::class, 'store'])->name('recruitment.store');

Route::middleware('auth', 'role:superadmin')->group(function () {
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Human Resources
        Route::resource('koordinators', KoordinatorController::class);
        Route::resource('enumerators', EnumeratorController::class);
        Route::get('enumerators/{id}/surat-tugas', [EnumeratorController::class, 'suratTugas'])->name('enumerators.surat-tugas');
        Route::get('enumerators/{id}/id-card', [EnumeratorController::class, 'idCard'])->name('enumerators.id-card');
        Route::resource('data-lapangans', DataLapanganController::class);
        Route::get('/datalapangan/{id}/download-foto-rumah-pdf', [DataLapanganController::class, 'downloadFotoRumahPdf'])
            ->name('datalapangan.download-foto-rumah-pdf');
        Route::post('data-lapangans/{dataLapangan}/update-status', [DataLapanganController::class, 'updateStatus'])
            ->name('data-lapangans.update-status');
        Route::post('data-lapangans/{dataLapangan}/update-status-payment', [DataLapanganController::class, 'updateStatusPayment'])
            ->name('data-lapangans.update-status-payment');
        Route::post('/data-lapangans/{id}/update-keterangan', [DataLapanganController::class, 'updateKeterangan'])->name('data-lapangans.update-keterangan');
        Route::post('data-lapangan/{dataLapangan}/upload-file', [DataLapanganController::class, 'uploadFile'])->name('data-lapangans.upload-file');
        Route::post('data-lapangans/{dataLapangan}/delete-file', [DataLapanganController::class, 'deleteFile'])->name('data-lapangans.delete-file');
        Route::get('laporan-harian', [LaporanHarianController::class, 'index'])->name('laporan-harian.index');
        // Recruitment
        Route::resource('recruitments', RecruitmentController::class);
        Route::post('recruitments/{id}/update-status', [RecruitmentController::class, 'updateStatus'])->name('recruitments.update-status');
        Route::get('recruitments/{id}/download-foto/{type}', [RecruitmentController::class, 'downloadFoto'])->name('recruitments.download-foto');
        // Finance Management
        Route::resource('arus-kas', CashflowController::class);
        Route::get('/cashflows/data', [CashflowController::class, 'getData'])->name('cashflows.data');
        Route::get('/cashflows', [CashflowController::class, 'cashflows'])->name('cashflow.index');
        // WA Gateway Fonnte
        Route::resource('devices', DeviceController::class);
        Route::post('send-message', [DeviceController::class, 'sendMessage'])->name('send.message');
        Route::post('devices/status', [DeviceController::class, 'checkDeviceStatus']);
        Route::post('devices/activate', [DeviceController::class, 'activateDevice'])->name('devices.activate');
        Route::post('devices/disconnect', [DeviceController::class, 'disconnect'])->name('devices.disconnect');

        // Management Users 
        Route::resource('users', UserController::class);

        // settings
        Route::get('/settings', [SettingwebsiteController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingwebsiteController::class, 'update'])->name('settings.update');
        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    Route::view('superadmin/dashboard', 'superadmin.home.index');
    // Route::get('/', function () {
    //     return view('superadmin.home.index')->name('superadmin.index');
    // });
});
/**
 * Koordinator Routes
 */
Route::middleware('auth', 'role:koordinator')->group(function () {
    Route::prefix('koordinator')->name('koordinator.')->group(function () {
        Route::get('dashboard', [KoordinatorDashboardController::class, 'index']);
        Route::get('/', [KoordinatorDashboardController::class, 'index'])->name('dashboard');

        // Data Lapangan
        Route::get('data-lapangan', [KoordinatorDataLapanganController::class, 'index'])->name('data-lapangan.index');
        Route::get('data-lapangan/{id}', [KoordinatorDataLapanganController::class, 'show'])->name('data-lapangan.show');
        // Data Pendamping
        // Data Pendamping
        Route::get('data-pendamping', [DataPendampingController::class, 'index'])->name('data-pendamping.index');
        Route::get('/cashflow', [CashflowKoordinatorController::class, 'index'])->name('cashflow.index');
        // settings
        Route::put('/settings', [SettingwebsiteController::class, 'update'])->name('settings.update');
        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    Route::view('superadmin/dashboard', 'superadmin.home.index');
    // Route::get('/', function () {
    //     return view('superadmin.home.index')->name('superadmin.index');
    // });
});


require __DIR__ . '/auth.php';
