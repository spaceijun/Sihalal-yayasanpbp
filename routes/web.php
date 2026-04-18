<?php

use App\Http\Controllers\AppVersionController;
use App\Http\Controllers\DataEntry\DashboardController as DataEntryDashboardController;
use App\Http\Controllers\DataEntry\DataEntryProgressController;
use App\Http\Controllers\DataEntry\DataLapanganController as DataEntryDataLapanganController;
use App\Http\Controllers\DataEntry\PengumumanDataEntryController;
use App\Http\Controllers\DataEntry\SettingAkunController;
use App\Http\Controllers\Enumerator\DashboardEnumController;
use App\Http\Controllers\Koordinator\CashflowKoordinatorController;
use App\Http\Controllers\Koordinator\DashboardController as KoordinatorDashboardController;
use App\Http\Controllers\Koordinator\DataLapanganController as KoordinatorDataLapanganController;
use App\Http\Controllers\Koordinator\DataPendampingController;
use App\Http\Controllers\Koordinator\RecruitmentController as KoordinatorRecruitmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\AppVersionController as SuperadminAppVersionController;
use App\Http\Controllers\Superadmin\CashflowController;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\DataEntryController;
use App\Http\Controllers\Superadmin\DataEntryPenagihanController;
use App\Http\Controllers\Superadmin\DataEntryProgressController as SuperadminDataEntryProgressController;
use App\Http\Controllers\Superadmin\DataLapanganController;
use App\Http\Controllers\Superadmin\DeviceController;
use App\Http\Controllers\Superadmin\EnumeratorController;
use App\Http\Controllers\Superadmin\KoordinatorController;
use App\Http\Controllers\Superadmin\LaporanHarianController;
use App\Http\Controllers\Superadmin\PengumumanController;
use App\Http\Controllers\Superadmin\RecruitmentController;
use App\Http\Controllers\Superadmin\SettingwebsiteController;
use App\Http\Controllers\Superadmin\SpotcheckController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\VerifikatorController;
use App\Http\Controllers\Superadmin\VerifikatorPaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', function () {
    return view('welcome');
});
// Redirect berdasarkan role jika sudah login
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        // Redirect berdasarkan role
        if ($user->role === 'superadmin') {
            return redirect('/superadmin');
        } elseif ($user->role === 'data-entry') {
            return redirect('/data-entry');
        }

        // Default redirect jika role lain
        return redirect('/dashboard');
    }

    return view('auth.login');
});
Route::get('formulir-halal', [DataLapanganController::class, 'create'])->name('formulir.halal');
Route::post('formulir-halal', [DataLapanganController::class, 'store'])->name('formulir.halal.store');
Route::post('upload/{type}', [DataLapanganController::class, 'uploadFileSequintal'])->name('upload.file');
Route::get('recruitment', [RecruitmentController::class, 'create'])->name('recruitment.formulir');
Route::post('recruitment', [RecruitmentController::class, 'store'])->name('recruitment.store');
Route::get('spotcheck', [SpotcheckController::class, 'create'])->name('spotcheck.formulir');
Route::post('spotcheck', [SpotcheckController::class, 'store'])->name('spotcheck.store');
Route::get('version/check', [AppVersionController::class, 'check']);
Route::get('/recruitment/confirm/{hashedId}', [RecruitmentController::class, 'confirm'])->name('recruitment.confirm');


Route::middleware('auth', 'role:superadmin')->group(function () {
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Human Resources
        Route::resource('koordinators', KoordinatorController::class);
        Route::resource('data-entries', DataEntryController::class);
        Route::resource('enumerators', EnumeratorController::class);
        Route::get('enumerators/{id}/surat-tugas', [EnumeratorController::class, 'suratTugas'])->name('enumerators.surat-tugas');
        Route::get('enumerators/{id}/id-card', [EnumeratorController::class, 'idCard'])->name('enumerators.id-card');
        Route::get('/data-lapangans/export', [DataLapanganController::class, 'export'])->name('data-lapangans.export');
        Route::patch('enumerators/{id}/aktivasi', [EnumeratorController::class, 'aktivasi'])->name('enumerators.aktivasi');
        Route::resource('data-lapangans', DataLapanganController::class);
        Route::get('/datalapangan/{id}/download-foto-rumah-pdf', [DataLapanganController::class, 'downloadFotoRumahPdf'])->name('datalapangan.download-foto-rumah-pdf');
        Route::post('data-lapangans/{dataLapangan}/update-status', [DataLapanganController::class, 'updateStatus'])->name('data-lapangans.update-status');
        Route::patch('data-lapangans/{dataLapangan}/update-status-payment', [DataLapanganController::class, 'updateStatusPayment'])->name('data-lapangans.update-status-payment');
        Route::post('data-lapangans/bulk-payment', [DataLapanganController::class, 'bulkUpdateStatusPayment'])->name('data-lapangans.bulk-payment');
        Route::post('/data-lapangans/{id}/update-keterangan', [DataLapanganController::class, 'updateKeterangan'])->name('data-lapangans.update-keterangan');
        Route::post('data-lapangan/{dataLapangan}/upload-file', [DataLapanganController::class, 'uploadFile'])->name('data-lapangans.upload-file');
        Route::post('data-lapangans/{dataLapangan}/delete-file', [DataLapanganController::class, 'deleteFile'])->name('data-lapangans.delete-file');
        Route::get('/datalapangan/{id}/download-foto-ktp', [DataLapanganController::class, 'downloadFotoKTP'])->name('datalapangan.download-foto-ktp');
        Route::get('laporan-harian', [LaporanHarianController::class, 'index'])->name('laporan-harian.index');
        Route::get('data-revisi', [DataLapanganController::class, 'dataRevisi'])->name('data-lapangans.data-revisi');
        Route::post('/data-revisi/{id}/send-notification', [DataLapanganController::class, 'sendRevisiNotification'])->name('data-revisi.send-notification');
        Route::post('/data-revisi/send-all-notifications', [DataLapanganController::class, 'sendAllRevisiNotifications'])->name('data-revisi.send-all-notifications');
        Route::get('/datalapangan/{id}/download-foto-pendamping', [DataLapanganController::class, 'downloadFotoPendamping'])->name('datalapangan.download-foto-pendamping');
        Route::get('/datalapangan/{id}/download-foto-produk', [DataLapanganController::class, 'downloadFotoProduk'])->name('datalapangan.download-foto-produk');
        Route::post('/data-lapangans/{id}/update-email', [DataLapanganController::class, 'updateEmail'])->name('data-lapangans.update-email');
        Route::patch('/data-lapangans/{id}/update-email-sihalal', [DataLapanganController::class, 'updateEmailSihalal'])->name('data-lapangans.update-email-sihalal');
        // Tagihan Data Entry
        Route::get('/penagihan', [DataEntryPenagihanController::class, 'index'])->name('penagihan.index');
        Route::post('/penagihan/{penagihan}/approve', [DataEntryPenagihanController::class, 'approve'])->name('penagihan.approve');
        Route::post('/penagihan/{penagihan}/proses', [DataEntryPenagihanController::class, 'proses'])->name('penagihan.proses');
        Route::post('/penagihan/{penagihan}/tolak', [DataEntryPenagihanController::class, 'tolak'])->name('penagihan.tolak');
        // Verifikators 
        Route::resource('verifikators', VerifikatorController::class);
        Route::post('verifikators/{verifikator}/bayar', [VerifikatorController::class, 'bayar'])->name('verifikators.bayar');
        Route::get('verifikators/{verifikator}/kalkulasi', [VerifikatorController::class, 'kalkulasi'])->name('verifikators.kalkulasi');
        Route::resource('verifikator-payments', VerifikatorPaymentController::class);
        // Spotcheck
        Route::resource('spotchecks', SpotcheckController::class);
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
        // Data Entry Progress
        Route::prefix('data-entry-progress')->name('data-entry-progress.')->group(function () {
            Route::get('/',                                    [SuperadminDataEntryProgressController::class, 'index'])->name('index');
            Route::get('/{progress}',                          [SuperadminDataEntryProgressController::class, 'show'])->name('show');
            Route::patch('/{progress}/terima',                 [SuperadminDataEntryProgressController::class, 'terima'])->name('terima');
            Route::patch('/{progress}/revisi',                 [SuperadminDataEntryProgressController::class, 'revisi'])->name('revisi');
            Route::patch('/{progress}/tolak',                  [SuperadminDataEntryProgressController::class, 'tolak'])->name('tolak');
            Route::post('/bulk-terima',                        [SuperadminDataEntryProgressController::class, 'bulkTerima'])->name('bulk-terima');
        });
        // Pengumuman
        Route::get('pengumumen', [PengumumanController::class, 'index'])->name('pengumumen.index');
        Route::get('pengumumen/create', [PengumumanController::class, 'create'])->name('pengumumen.create');
        Route::post('pengumumen', [PengumumanController::class, 'store'])->name('pengumumen.store');
        Route::get('pengumumen/{hashedId}', [PengumumanController::class, 'show'])->name('pengumumen.show');
        Route::get('pengumumen/{hashedId}/edit', [PengumumanController::class, 'edit'])->name('pengumumen.edit');
        Route::put('pengumumen/{pengumuman}', [PengumumanController::class, 'update'])->name('pengumumen.update');
        Route::delete('pengumumen/{id}', [PengumumanController::class, 'destroy'])->name('pengumumen.destroy');
        // Management Users 
        Route::resource('users', UserController::class);

        // settings
        Route::get('/settings', [SettingwebsiteController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingwebsiteController::class, 'update'])->name('settings.update');
        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        // App Version 
        Route::resource('app-versions', SuperadminAppVersionController::class);
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
        Route::put('data-lapangans/{id}/update-status', [KoordinatorDataLapanganController::class, 'updateStatus'])->name('datalapangan.update-status');
        Route::get('/datalapangan/{id}/download-foto-ktp', [DataLapanganController::class, 'downloadFotoKTP'])->name('datalapangan.download-foto-ktp');
        Route::get('/datalapangan/{id}/download-foto-pendamping', [DataLapanganController::class, 'downloadFotoPendamping'])->name('datalapangan.download-foto-pendamping');
        Route::get('/datalapangan/{id}/download-foto-produk', [DataLapanganController::class, 'downloadFotoProduk'])->name('datalapangan.download-foto-produk');


        // Data Pendamping
        Route::get('data-pendamping', [DataPendampingController::class, 'index'])->name('data-pendamping.index');
        Route::get('data-pendamping/{id}', [DataPendampingController::class, 'show'])->name('data-pendamping.show');
        Route::get('data-pendamping/{id}/surat-tugas', [DataPendampingController::class, 'suratTugas'])->name('data-pendamping.surat-tugas');
        Route::get('data-pendamping/{id}/id-card', [DataPendampingController::class, 'idCard'])->name('data-pendamping.id-card');
        Route::get('data-pendamping/{id}/data-lapangan', [DataPendampingController::class, 'dataLapangan'])->name('data-pendamping.data-lapangan');
        Route::get('/cashflow', [CashflowKoordinatorController::class, 'index'])->name('cashflow.index');

        // Recruitments
        Route::resource('recruitments', KoordinatorRecruitmentController::class);
        Route::post('recruitments/{id}/update-status', [KoordinatorRecruitmentController::class, 'updateStatus'])->name('recruitments.update-status');
        Route::get('recruitments/{id}/download-foto/{type}', [KoordinatorRecruitmentController::class, 'downloadFoto'])->name('recruitments.download-foto');

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

/**
 * DATA ENTRY ROUTES
 */
Route::middleware('auth', 'role:data_entry')->group(function () {
    Route::prefix('data-entry')->name('data-entry.')->group(function () {
        Route::get('dashboard', [DataEntryDashboardController::class, 'index']);
        Route::get('/', [DataEntryDashboardController::class, 'index'])->name('dashboard');
        Route::post('dashboard/mark-pengumuman-read', [DataEntryDashboardController::class, 'markPengumumanRead'])
            ->name('markPengumumanRead');

        // Data Lapangan
        Route::get('data-lapangan', [DataEntryDataLapanganController::class, 'index'])->name('data-lapangan.index');
        Route::get('data-lapangan/{hashedId}', [DataEntryDataLapanganController::class, 'show'])->name('data-lapangan.show');
        Route::post('data-lapangan/{dataLapangan}/upload-file', [DataEntryDataLapanganController::class, 'uploadFile'])->name('data-lapangan.upload-file');
        Route::put('data-lapangans/{id}/update-status', [DataEntryDataLapanganController::class, 'updateStatus'])->name('datalapangan.update-status');
        Route::patch('data-lapangan/{dataLapangan}/resubmit', [DataEntryDataLapanganController::class, 'resubmit'])->name('data-lapangan.resubmit');
        Route::post('{id}/lock', [DataEntryDataLapanganController::class, 'lockData']);
        Route::delete('{id}/lock', [DataEntryDataLapanganController::class, 'unlockData']);
        // Download
        Route::get('datalapangan/{id}/download-foto-rumah-pdf', [DataLapanganController::class, 'downloadFotoRumahPdf'])->name('datalapangan.download-foto-rumah-pdf');
        Route::get('datalapangan/{id}/download-foto-ktp', [DataLapanganController::class, 'downloadFotoKTP'])->name('datalapangan.download-foto-ktp');
        Route::get('datalapangan/{id}/download-foto-pendamping', [DataLapanganController::class, 'downloadFotoPendamping'])->name('datalapangan.download-foto-pendamping');
        Route::get('datalapangan/{id}/download-foto-produk', [DataLapanganController::class, 'downloadFotoProduk'])->name('datalapangan.download-foto-produk');
        // Pengumuman
        Route::get('pengumumen', [PengumumanDataEntryController::class, 'index'])->name('pengumumen.index');
        Route::get('pengumumen/{hashedId}', [PengumumanDataEntryController::class, 'show'])->name('pengumumen.show');

        // Progress
        Route::get('progress', [DataEntryProgressController::class, 'index'])->name('progress.index');
        Route::get('progress/{id}', [DataEntryProgressController::class, 'show'])->name('progress.show');

        // settings
        Route::get('manajemen-akun', [SettingAkunController::class, 'index'])->name('manajemen-akun.index');
        Route::post('manajemen-akun/update', [SettingAkunController::class, 'update'])->name('manajemen-akun.update');
    });
});
/**
 * 
 * ENUMERATOR ROUTES
 */
Route::middleware('auth', 'role:enumerator')->group(function () {
    Route::prefix('enumerator')->name('enumerator.')->group(function () {
        Route::get('dashboard', [DashboardEnumController::class, 'index']);
        Route::get('/', [DashboardEnumController::class, 'index'])->name('dashboard');
        Route::get('data-lapangan', [DataEntryDataLapanganController::class, 'index'])->name('data-lapangan.index');
        Route::get('data-lapangan', [DataEntryDataLapanganController::class, 'index'])->name('data-lapangan.index');
        Route::put('data-lapangans/{id}/update-status', [DataEntryDataLapanganController::class, 'updateStatus'])->name('datalapangan.update-status');
        Route::get('datalapangan/{id}/download-foto-rumah-pdf', [DataLapanganController::class, 'downloadFotoRumahPdf'])->name('datalapangan.download-foto-rumah-pdf');
        Route::get('datalapangan/{id}/download-foto-ktp', [DataLapanganController::class, 'downloadFotoKTP'])->name('datalapangan.download-foto-ktp');
        Route::get('datalapangan/{id}/download-foto-pendamping', [DataLapanganController::class, 'downloadFotoPendamping'])->name('datalapangan.download-foto-pendamping');
        Route::get('datalapangan/{id}/download-foto-produk', [DataLapanganController::class, 'downloadFotoProduk'])->name('datalapangan.download-foto-produk');
        Route::get('data-lapangan/{id}', [DataEntryDataLapanganController::class, 'show'])->name('data-lapangan.show');
        Route::post('data-lapangan/{dataLapangan}/upload-file', [DataEntryDataLapanganController::class, 'uploadFile'])->name('data-lapangan.upload-file');
        Route::get('progress', [DataEntryProgressController::class, 'index'])->name('progress.index');
        Route::get('progress/{id}', [DataEntryProgressController::class, 'show'])->name('progress.show');
    });
});



require __DIR__ . '/auth.php';
