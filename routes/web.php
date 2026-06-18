<?php

use App\Http\Controllers\AdminUmum\DashboardController as AdminUmumDashboardController;
use App\Http\Controllers\Api\DataEntryProgressController as DataEntryProgressApiController;
use App\Http\Controllers\Api\FaceMatchController;
use App\Http\Controllers\AppVersionController;
use App\Http\Controllers\DataEntry\DashboardController as DataEntryDashboardController;
use App\Http\Controllers\DataEntry\DataEntryProgressController;
use App\Http\Controllers\DataEntry\DataLapanganController as DataEntryDataLapanganController;
use App\Http\Controllers\DataEntry\PengumumanDataEntryController;
use App\Http\Controllers\DataEntry\SettingAkunController;
use App\Http\Controllers\DataEntry\TicketsEntryController;
use App\Http\Controllers\Enumerator\DashboardEnumController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\AppVersionController as SuperadminAppVersionController;
use App\Http\Controllers\Superadmin\CashflowController;
use App\Http\Controllers\Superadmin\DashboardController;
use App\Http\Controllers\Superadmin\DataEntryController;
use App\Http\Controllers\Superadmin\DataEntryPenagihanController;
use App\Http\Controllers\Superadmin\DataEntryProgressController as SuperadminDataEntryProgressController;
use App\Http\Controllers\Superadmin\DataLapanganController;
use App\Http\Controllers\Superadmin\DeviceController;
use App\Http\Controllers\Superadmin\DiagnosticController;
use App\Http\Controllers\Superadmin\EnumeratorController;
use App\Http\Controllers\Superadmin\KoordinatorController;
use App\Http\Controllers\Superadmin\LaporanHarianController;
use App\Http\Controllers\Superadmin\PengumumanController;
use App\Http\Controllers\Superadmin\RankingPendampingController;
use App\Http\Controllers\Superadmin\RecruitmentApplicantController;
use App\Http\Controllers\Superadmin\RecruitmentPostController;
use App\Http\Controllers\Superadmin\ResepMakananController;
use App\Http\Controllers\Superadmin\ServerInfoController;
use App\Http\Controllers\Superadmin\SettingwebsiteController;
use App\Http\Controllers\Superadmin\SpotcheckController;
use App\Http\Controllers\Superadmin\TicketController;
use App\Http\Controllers\Superadmin\TicketPendampingController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\VerifikatorController;
use App\Http\Controllers\Superadmin\VerifikatorPaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
        } elseif ($user->role === 'admin_umum') {
            return redirect('/admin-umum');
        } elseif ($user->role === 'data-entry') {
            return redirect('/data-entry');
        }

        // Default redirect jika role lain
        return redirect('/dashboard');
    }

    return view('auth.login');
});
// Formulir Halal — publik (tanpa auth), dilindungi rate limiter
Route::middleware('throttle:30,1')->group(function () {
    Route::get('formulir-halal', [DataLapanganController::class, 'create'])->name('formulir.halal');
});
Route::middleware('throttle:formulir-halal')->group(function () {
    Route::post('formulir-halal', [DataLapanganController::class, 'store'])->name('formulir.halal.store');
});
Route::middleware('throttle:upload-file')->group(function () {
    Route::post('upload/{type}', [DataLapanganController::class, 'uploadFileSequintal'])->name('upload.file');
});
Route::get('spotcheck', [SpotcheckController::class, 'create'])->name('spotcheck.formulir');
Route::post('spotcheck', [SpotcheckController::class, 'store'])->name('spotcheck.store');
Route::get('version/check', [AppVersionController::class, 'check']);
Route::get('/recruitment/confirm/{hashedId}', [RecruitmentApplicantController::class, 'confirm'])->name('recruitment.confirm');
// Lowongan Pekerjaan — Form Publik Dinamis
Route::get('loker/{slug}', [RecruitmentApplicantController::class, 'form'])->name('recruitment.form');
Route::post('loker/{slug}', [RecruitmentApplicantController::class, 'submit'])->name('recruitment.form.submit');
Route::get('resep-makanan', [HomeController::class, 'resepMakanan'])->name('resep-makanan');

Route::middleware('auth', 'role:superadmin')->group(function () {
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        // Server Info
        Route::get('/server-info', [ServerInfoController::class, 'index'])->name('server-info');
        Route::get('/server-info/realtime', [ServerInfoController::class, 'realtime'])->name('server-info.realtime');

        // Diagnostic
        Route::get('/diagnostic', [DiagnosticController::class, 'index'])->name('diagnostic.index');
        Route::post('/diagnostic/run', [DiagnosticController::class, 'run'])->name('diagnostic.run');

        // Ranking Pendamping
        Route::get('ranking-pendamping', [RankingPendampingController::class, 'index'])->name('ranking-pendamping.index');

        // Human Resources
        Route::resource('koordinators', KoordinatorController::class);
        Route::get('/koordinators-data', [KoordinatorController::class, 'data'])->name('koordinators.data');
        Route::resource('data-entries', DataEntryController::class);
        Route::get('/data-entries-data', [DataEntryController::class, 'data'])->name('data-entries.data');
        Route::get('/enumerators/export-pdf', [EnumeratorController::class, 'exportPdf'])->name('enumerators.export-pdf');
        Route::resource('enumerators', EnumeratorController::class);
        Route::get('/enumerators-data', [EnumeratorController::class, 'data'])->name('enumerators.data');
        Route::post('/enumerators/{id}/generate-user', [EnumeratorController::class, 'generateUser'])->name('enumerators.generate-user');
        Route::get('enumerators/{id}/surat-tugas', [EnumeratorController::class, 'suratTugas'])->name('enumerators.surat-tugas');
        Route::get('enumerators/{id}/id-card', [EnumeratorController::class, 'idCard'])->name('enumerators.id-card');
        Route::get('/data-lapangans/export', [DataLapanganController::class, 'export'])->name('data-lapangans.export');
        Route::get('/data-lapangans/data', [DataLapanganController::class, 'data'])->name('data-lapangans.data');
        Route::patch('enumerators/{id}/aktivasi', [EnumeratorController::class, 'aktivasi'])->name('enumerators.aktivasi');
        Route::get('/enumerators/{id}/gallery', [EnumeratorController::class, 'gallery'])->name('enumerators.gallery');
        Route::get('/enumerators/{id}/download-foto/{type}', [EnumeratorController::class, 'downloadFoto'])->name('enumerators.download-foto');
        Route::get('/enumerators/{id}/download-foto/{dataId}/{type}', [EnumeratorController::class, 'downloadFotoByEntry'])->name('enumerators.download-foto-entry');
        // Data Lapangan
        Route::resource('data-lapangans', DataLapanganController::class);
        Route::get('/datalapangan/{id}/download-foto-rumah-pdf', [DataLapanganController::class, 'downloadFotoRumahPdf'])->name('datalapangan.download-foto-rumah-pdf');
        Route::post('data-lapangans/{dataLapangan}/update-status', [DataLapanganController::class, 'updateStatus'])->name('data-lapangans.update-status');
        Route::patch('data-lapangans/{dataLapangan}/update-status-payment', [DataLapanganController::class, 'updateStatusPayment'])->name('data-lapangans.update-status-payment');
        Route::post('data-lapangans/bulk-payment', [DataLapanganController::class, 'bulkUpdateStatusPayment'])->name('data-lapangans.bulk-payment');
        Route::post('data-lapangans/{id}/toggle-unlock', [DataLapanganController::class, 'toggleUnlockForDataEntry'])->name('data-lapangans.toggle-unlock');
        Route::post('/data-lapangans/{id}/update-keterangan', [DataLapanganController::class, 'updateKeterangan'])->name('data-lapangans.update-keterangan');
        Route::post('data-lapangan/{dataLapangan}/upload-file', [DataLapanganController::class, 'uploadFile'])->name('data-lapangans.upload-file');
        Route::post('data-lapangans/{dataLapangan}/delete-file', [DataLapanganController::class, 'deleteFile'])->name('data-lapangans.delete-file');
        Route::get('/datalapangan/{id}/download-foto-ktp', [DataLapanganController::class, 'downloadFotoKTP'])->name('datalapangan.download-foto-ktp');
        Route::get('laporan-harian', [LaporanHarianController::class, 'index'])->name('laporan-harian.index');
        Route::get('data-revisi', [DataLapanganController::class, 'dataRevisi'])->name('data-lapangans.data-revisi');
        Route::get('/data-revisi/export-pdf', [DataLapanganController::class, 'exportRevisiPdf'])->name('data-revisi.export-pdf');
        Route::post('/data-revisi/{id}/send-notification', [DataLapanganController::class, 'sendRevisiNotification'])->name('data-revisi.send-notification');
        Route::post('/data-revisi/send-all-notifications', [DataLapanganController::class, 'sendAllRevisiNotifications'])->name('data-revisi.send-all-notifications');
        Route::get('/datalapangan/{id}/download-foto-pendamping', [DataLapanganController::class, 'downloadFotoPendamping'])->name('datalapangan.download-foto-pendamping');
        Route::get('/datalapangan/{id}/download-foto-produk', [DataLapanganController::class, 'downloadFotoProduk'])->name('datalapangan.download-foto-produk');
        Route::post('/data-lapangans/{id}/update-email', [DataLapanganController::class, 'updateEmail'])->name('data-lapangans.update-email');
        Route::get('data-lapangans/check-email', [DataLapanganController::class, 'checkEmail'])->name('data-lapangans.check-email');
        Route::patch('/data-lapangans/{id}/update-email-sihalal', [DataLapanganController::class, 'updateEmailSihalal'])->name('data-lapangans.update-email-sihalal');
        // Tagihan Data Entry
        Route::get('/penagihan', [DataEntryPenagihanController::class, 'index'])->name('penagihan.index');
        Route::post('/penagihan/{penagihan}/approve', [DataEntryPenagihanController::class, 'approve'])->name('penagihan.approve');
        Route::post('/penagihan/{penagihan}/proses', [DataEntryPenagihanController::class, 'proses'])->name('penagihan.proses');
        Route::post('/penagihan/{penagihan}/tolak', [DataEntryPenagihanController::class, 'tolak'])->name('penagihan.tolak');
        Route::get('/penagihan/{penagihan}/receipt', [DataEntryPenagihanController::class, 'downloadReceipt'])->name('penagihan.receipt');
        // Verifikators
        Route::resource('verifikators', VerifikatorController::class);
        Route::post('verifikators/{verifikator}/bayar', [VerifikatorController::class, 'bayar'])->name('verifikators.bayar');
        Route::get('verifikators/{verifikator}/kalkulasi', [VerifikatorController::class, 'kalkulasi'])->name('verifikators.kalkulasi');
        Route::resource('verifikator-payments', VerifikatorPaymentController::class);
        // Spotcheck
        Route::resource('spotchecks', SpotcheckController::class);
        // Recruitment — Lowongan Pekerjaan
        Route::get('recruitment-posts-data', [RecruitmentPostController::class, 'data'])->name('recruitment-posts.data');
        Route::patch('recruitment-posts/{id}/toggle', [RecruitmentPostController::class, 'toggle'])->name('recruitment-posts.toggle');
        Route::resource('recruitment-posts', RecruitmentPostController::class);
        // Recruitment — Pelamar (update status dari halaman show lowongan)
        Route::post('recruitments/{id}/update-status-v2', [RecruitmentApplicantController::class, 'updateStatus'])->name('recruitments.update-status-v2');
        Route::get('recruitments/{id}', [RecruitmentApplicantController::class, 'show'])->name('recruitments.show');

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
            Route::get('/', [SuperadminDataEntryProgressController::class, 'index'])->name('index');
            Route::get('/data', [SuperadminDataEntryProgressController::class, 'data'])->name('data');
            Route::get('/{progress}', [SuperadminDataEntryProgressController::class, 'show'])->name('show');
            Route::patch('/{progress}/terima', [SuperadminDataEntryProgressController::class, 'terima'])->name('terima');
            Route::patch('/{progress}/revisi', [SuperadminDataEntryProgressController::class, 'revisi'])->name('revisi');
            Route::patch('/{progress}/tolak', [SuperadminDataEntryProgressController::class, 'tolak'])->name('tolak');
            Route::post('/bulk-terima', [SuperadminDataEntryProgressController::class, 'bulkTerima'])->name('bulk-terima');
        });
        // Resep Makanan
        Route::resource('resep-makanans', ResepMakananController::class);
        // Pengumuman
        Route::prefix('pengumumen')->name('pengumumen.')->group(function () {
            Route::get('/', [PengumumanController::class, 'index'])->name('index');
            Route::get('/create', [PengumumanController::class, 'create'])->name('create');
            Route::post('/', [PengumumanController::class, 'store'])->name('store');
            Route::get('/{hashedId}', [PengumumanController::class, 'show'])->name('show');
            Route::get('/{hashedId}/edit', [PengumumanController::class, 'edit'])->name('edit');
            Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
            Route::delete('/{id}', [PengumumanController::class, 'destroy'])->name('destroy');
        });
        // Management Users
        Route::resource('users', UserController::class);
        Route::get('/users-data', [UserController::class, 'data'])->name('users.data');
        // Ticket (Data Entry)
        Route::resource('tickets', TicketController::class)->only(['index', 'show', 'destroy']);
        Route::patch('tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');

        // Ticket Pendamping (Enumerator)
        Route::get('ticket-pendampings-data', [TicketPendampingController::class, 'data'])->name('ticket-pendampings.data');
        Route::get('ticket-pendampings', [TicketPendampingController::class, 'index'])->name('ticket-pendampings.index');
        Route::get('ticket-pendampings/{id}', [TicketPendampingController::class, 'show'])->name('ticket-pendampings.show');
        Route::patch('ticket-pendampings/{id}/status', [TicketPendampingController::class, 'updateStatus'])->name('ticket-pendampings.update-status');
        Route::delete('ticket-pendampings/{id}', [TicketPendampingController::class, 'destroy'])->name('ticket-pendampings.destroy');

        // settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingwebsiteController::class, 'index'])->name('index');
            Route::put('/settings', [SettingwebsiteController::class, 'update'])->name('update');
            Route::put('/settings/env', [SettingwebsiteController::class, 'updateEnv'])->name('env.update');
        });
        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('update');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        });
        // App Version
        Route::resource('app-versions', SuperadminAppVersionController::class);

        // Face Match
        Route::prefix('face-match')->name('face-match.')->group(function () {
            Route::get('/', [FaceMatchController::class, 'index'])->name('index');
            Route::post('/match', [FaceMatchController::class, 'match'])->name('match');
            Route::get('/status', [FaceMatchController::class, 'status'])->name('status');
            Route::get('/poll', [FaceMatchController::class, 'poll'])->name('poll');
            Route::get('/result', [FaceMatchController::class, 'result'])->name('result');
        });
    });
    Route::view('superadmin/dashboard', 'superadmin.home.index');
    // Route::get('/', function () {
    //     return view('superadmin.home.index')->name('superadmin.index');
    // });
});

/**
 * ADMIN UMUM ROUTES
 */
Route::middleware('auth', 'role:admin_umum')->group(function () {
    Route::prefix('admin-umum')->name('admin-umum.')->group(function () {
        Route::get('/', [AdminUmumDashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard', [AdminUmumDashboardController::class, 'index']);

        // Data Lapangan (read-only operations & status update)
        Route::get('/data-lapangans/export', [DataLapanganController::class, 'export'])->name('data-lapangans.export');
        Route::get('/data-lapangans/data', [DataLapanganController::class, 'data'])->name('data-lapangans.data');
        Route::get('/data-lapangans/check-email', [DataLapanganController::class, 'checkEmail'])->name('data-lapangans.check-email');
        Route::resource('data-lapangans', DataLapanganController::class);
        Route::get('/datalapangan/{id}/download-foto-rumah-pdf', [DataLapanganController::class, 'downloadFotoRumahPdf'])->name('datalapangan.download-foto-rumah-pdf');
        Route::post('data-lapangans/{dataLapangan}/update-status', [DataLapanganController::class, 'updateStatus'])->name('data-lapangans.update-status');
        Route::patch('data-lapangans/{dataLapangan}/update-status-payment', [DataLapanganController::class, 'updateStatusPayment'])->name('data-lapangans.update-status-payment');
        Route::post('data-lapangans/bulk-payment', [DataLapanganController::class, 'bulkUpdateStatusPayment'])->name('data-lapangans.bulk-payment');
        Route::post('data-lapangans/{id}/toggle-unlock', [DataLapanganController::class, 'toggleUnlockForDataEntry'])->name('data-lapangans.toggle-unlock');
        Route::post('/data-lapangans/{id}/update-keterangan', [DataLapanganController::class, 'updateKeterangan'])->name('data-lapangans.update-keterangan');
        Route::post('data-lapangan/{dataLapangan}/upload-file', [DataLapanganController::class, 'uploadFile'])->name('data-lapangans.upload-file');
        Route::post('data-lapangans/{dataLapangan}/delete-file', [DataLapanganController::class, 'deleteFile'])->name('data-lapangans.delete-file');
        Route::get('/datalapangan/{id}/download-foto-ktp', [DataLapanganController::class, 'downloadFotoKTP'])->name('datalapangan.download-foto-ktp');
        Route::get('data-revisi', [DataLapanganController::class, 'dataRevisi'])->name('data-lapangans.data-revisi');
        Route::get('/data-revisi/export-pdf', [DataLapanganController::class, 'exportRevisiPdf'])->name('data-revisi.export-pdf');
        Route::post('/data-revisi/{id}/send-notification', [DataLapanganController::class, 'sendRevisiNotification'])->name('data-revisi.send-notification');
        Route::post('/data-revisi/send-all-notifications', [DataLapanganController::class, 'sendAllRevisiNotifications'])->name('data-revisi.send-all-notifications');
        Route::get('/datalapangan/{id}/download-foto-pendamping', [DataLapanganController::class, 'downloadFotoPendamping'])->name('datalapangan.download-foto-pendamping');
        Route::get('/datalapangan/{id}/download-foto-produk', [DataLapanganController::class, 'downloadFotoProduk'])->name('datalapangan.download-foto-produk');
        Route::post('/data-lapangans/{id}/update-email', [DataLapanganController::class, 'updateEmail'])->name('data-lapangans.update-email');
        Route::patch('/data-lapangans/{id}/update-email-sihalal', [DataLapanganController::class, 'updateEmailSihalal'])->name('data-lapangans.update-email-sihalal');

        // Laporan Harian
        Route::get('laporan-harian', [LaporanHarianController::class, 'index'])->name('laporan-harian.index');

        // Human Resources — Koordinator
        Route::resource('koordinators', KoordinatorController::class);
        Route::get('/koordinators-data', [KoordinatorController::class, 'data'])->name('koordinators.data');

        // Human Resources — Data Entry
        Route::resource('data-entries', DataEntryController::class);
        Route::get('/data-entries-data', [DataEntryController::class, 'data'])->name('data-entries.data');

        // Human Resources — Enumerator
        Route::get('/enumerators/export-pdf', [EnumeratorController::class, 'exportPdf'])->name('enumerators.export-pdf');
        Route::resource('enumerators', EnumeratorController::class);
        Route::get('/enumerators-data', [EnumeratorController::class, 'data'])->name('enumerators.data');
        Route::post('/enumerators/{id}/generate-user', [EnumeratorController::class, 'generateUser'])->name('enumerators.generate-user');
        Route::get('enumerators/{id}/surat-tugas', [EnumeratorController::class, 'suratTugas'])->name('enumerators.surat-tugas');
        Route::get('enumerators/{id}/id-card', [EnumeratorController::class, 'idCard'])->name('enumerators.id-card');
        Route::patch('enumerators/{id}/aktivasi', [EnumeratorController::class, 'aktivasi'])->name('enumerators.aktivasi');
        Route::get('/enumerators/{id}/gallery', [EnumeratorController::class, 'gallery'])->name('enumerators.gallery');
        Route::get('/enumerators/{id}/download-foto/{type}', [EnumeratorController::class, 'downloadFoto'])->name('enumerators.download-foto');
        Route::get('/enumerators/{id}/download-foto/{dataId}/{type}', [EnumeratorController::class, 'downloadFotoByEntry'])->name('enumerators.download-foto-entry');

        // Ranking Pendamping
        Route::get('ranking-pendamping', [RankingPendampingController::class, 'index'])->name('ranking-pendamping.index');

        // Spotcheck
        Route::resource('spotchecks', SpotcheckController::class);

        // Recruitment — Lowongan Pekerjaan
        Route::get('recruitment-posts-data', [RecruitmentPostController::class, 'data'])->name('recruitment-posts.data');
        Route::patch('recruitment-posts/{id}/toggle', [RecruitmentPostController::class, 'toggle'])->name('recruitment-posts.toggle');
        Route::resource('recruitment-posts', RecruitmentPostController::class);
        Route::post('recruitments/{id}/update-status-v2', [RecruitmentApplicantController::class, 'updateStatus'])->name('recruitments.update-status-v2');
        Route::get('recruitments/{id}', [RecruitmentApplicantController::class, 'show'])->name('recruitments.show');

        // Pengumuman
        Route::prefix('pengumumen')->name('pengumumen.')->group(function () {
            Route::get('/', [PengumumanController::class, 'index'])->name('index');
            Route::get('/create', [PengumumanController::class, 'create'])->name('create');
            Route::post('/', [PengumumanController::class, 'store'])->name('store');
            Route::get('/{hashedId}', [PengumumanController::class, 'show'])->name('show');
            Route::get('/{hashedId}/edit', [PengumumanController::class, 'edit'])->name('edit');
            Route::put('/{pengumuman}', [PengumumanController::class, 'update'])->name('update');
            Route::delete('/{id}', [PengumumanController::class, 'destroy'])->name('destroy');
        });

        // Ticket (Data Entry)
        Route::resource('tickets', TicketController::class)->only(['index', 'show', 'destroy']);
        Route::patch('tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');

        // Ticket Pendamping (Enumerator)
        Route::get('ticket-pendampings-data', [TicketPendampingController::class, 'data'])->name('ticket-pendampings.data');
        Route::get('ticket-pendampings', [TicketPendampingController::class, 'index'])->name('ticket-pendampings.index');
        Route::get('ticket-pendampings/{id}', [TicketPendampingController::class, 'show'])->name('ticket-pendampings.show');
        Route::patch('ticket-pendampings/{id}/status', [TicketPendampingController::class, 'updateStatus'])->name('ticket-pendampings.update-status');
        Route::delete('ticket-pendampings/{id}', [TicketPendampingController::class, 'destroy'])->name('ticket-pendampings.destroy');

        // Master Data — Resep Makanan
        Route::resource('resep-makanans', ResepMakananController::class);

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('update');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        });
    });
});

/**
 * DATA ENTRY ROUTES
 */
Route::middleware('auth', 'role:data_entry')->group(function () {
    Route::get('/api/data-entry/progress', [DataEntryProgressApiController::class, 'index']);
    Route::prefix('data-entry')->name('data-entry.')->group(function () {
        Route::get('dashboard', [DataEntryDashboardController::class, 'index']);
        Route::get('/', [DataEntryDashboardController::class, 'index'])->name('dashboard');
        Route::post('dashboard/mark-pengumuman-read', [DataEntryDashboardController::class, 'markPengumumanRead'])
            ->name('markPengumumanRead');

        // Data Lapangan
        Route::get('data-lapangan/data', [DataEntryDataLapanganController::class, 'data'])->name('data-lapangan.data');
        Route::get('data-lapangan', [DataEntryDataLapanganController::class, 'index'])->name('data-lapangan.index');
        Route::get('data-lapangan/{hashedId}', [DataEntryDataLapanganController::class, 'show'])->name('data-lapangan.show');
        Route::post('data-lapangan/{dataLapangan}/upload-file', [DataEntryDataLapanganController::class, 'uploadFile'])->name('data-lapangan.upload-file');
        Route::put('data-lapangans/{id}/update-status', [DataEntryDataLapanganController::class, 'updateStatus'])->name('datalapangan.update-status');
        Route::patch('data-lapangan/{dataLapangan}/resubmit', [DataEntryDataLapanganController::class, 'resubmit'])->name('data-lapangan.resubmit');
        Route::post('data-lapangan/{id}/lock', [DataEntryDataLapanganController::class, 'lockData'])->name('data-lapangan.lock');
        Route::delete('data-lapangan/{id}/lock', [DataEntryDataLapanganController::class, 'unlockData'])->name('data-lapangan.unlock');
        Route::post('data-lapangan/{id}/unlock-beacon', [DataEntryDataLapanganController::class, 'unlockBeacon'])->name('data-lapangan.unlock-beacon');

        // Download
        Route::get('datalapangan/{id}/download-foto-rumah-pdf', [DataLapanganController::class, 'downloadFotoRumahPdf'])->name('datalapangan.download-foto-rumah-pdf');
        Route::get('datalapangan/{id}/download-foto-ktp', [DataLapanganController::class, 'downloadFotoKTP'])->name('datalapangan.download-foto-ktp');
        Route::get('datalapangan/{id}/download-foto-pendamping', [DataLapanganController::class, 'downloadFotoPendamping'])->name('datalapangan.download-foto-pendamping');
        Route::get('datalapangan/{id}/download-foto-produk', [DataLapanganController::class, 'downloadFotoProduk'])->name('datalapangan.download-foto-produk');
        // Pengumuman
        Route::get('pengumumen', [PengumumanDataEntryController::class, 'index'])->name('pengumumen.index');
        Route::get('pengumumen/{hashedId}', [PengumumanDataEntryController::class, 'show'])->name('pengumumen.show');

        // Progress
        Route::get('progress/data', [DataEntryProgressController::class, 'data'])->name('progress.data');
        Route::get('progress', [DataEntryProgressController::class, 'index'])->name('progress.index');
        Route::get('progress/{id}', [DataEntryProgressController::class, 'show'])->name('progress.show');

        // Ticket
        Route::resource('tickets', TicketsEntryController::class);

        // settings
        Route::get('manajemen-akun', [SettingAkunController::class, 'index'])->name('manajemen-akun.index');
        Route::post('manajemen-akun/update', [SettingAkunController::class, 'update'])->name('manajemen-akun.update');
    });
});
/**
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
        Route::get('progress/data', [DataEntryProgressController::class, 'data'])->name('progress.data');
        Route::get('progress', [DataEntryProgressController::class, 'index'])->name('progress.index');
        Route::get('progress/{id}', [DataEntryProgressController::class, 'show'])->name('progress.show');
    });
});

require __DIR__.'/auth.php';
