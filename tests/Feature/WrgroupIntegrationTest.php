<?php

use App\Models\Cashflow;
use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;
use App\Models\Superadmin\WrgroupKomisiPembayaran;
use App\Models\Superadmin\WrgroupOutbox;
use App\Models\User;
use App\Services\Integrasi\Wrgroup\WrgroupDeliveryService;
use App\Services\Integrasi\Wrgroup\WrgroupEventService;
use App\Services\Superadmin\CashflowService;
use Database\Seeders\SettingwebSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // View::composer('*') di SettingwebsiteProvider query Settingwebsite::first() pada setiap
    // render view (termasuk halaman error bila ada exception tak tertangani) — tanpa baris ini
    // gagal dengan "no such table: settingwebsites" walau tidak terkait WRGROUP sama sekali.
    $this->seed(SettingwebSeeder::class);
});

function wrgroupAktifUji(): void
{
    config([
        'wrgroup.enabled' => true,
        'wrgroup.business_id' => 'BIZ-TEST',
        'wrgroup.api_key' => 'key-test',
        'wrgroup.api_secret' => 'secret-test',
        'wrgroup.api_url' => 'https://wrgroup.test/api/v1',
    ]);
}

function dataLapanganBelumBayarUji(array $override = []): DataLapangan
{
    $user = User::factory()->create();
    $koordinator = Koordinator::create([
        'user_id' => $user->id,
        'nama_lengkap' => 'Koordinator Uji',
        'email' => 'koordinator'.uniqid().'@example.com',
        'telephone' => '081200000000',
        'alamat' => 'Alamat Koordinator',
        'status' => 'Aktif',
    ]);
    $enumerator = Enumerator::create([
        'koordinator_id' => $koordinator->id,
        'nama_lengkap' => 'Enumerator Uji',
        'telephone' => '081300000000',
        'no_registrasi' => 'ENUM-'.uniqid(),
        'alamat' => 'Alamat Enumerator',
        'status' => 'Aktif',
    ]);

    return DataLapangan::create(array_merge([
        'enumerator_id' => $enumerator->id,
        'nama_pu' => 'Budi Santoso',
        'nik' => '3201012501900001',
        'alamat' => 'Jl. Merdeka No. 1',
        'titik_koordinat' => '-6.914744,107.609810',
        'foto_ktp' => 'foto/ktp.jpg',
        'foto_rumah' => 'foto/rumah.jpg',
        'foto_pendamping' => 'foto/pendamping.jpg',
        'foto_proses' => 'foto/proses.jpg',
        'foto_produk' => 'foto/produk.jpg',
    ], $override));
}

test('kasus DIBAYAR dengan WRGROUP_ENABLED=false tidak mencatat apa pun (no-op)', function () {
    $dataLapangan = dataLapanganBelumBayarUji();

    expect(config('wrgroup.enabled'))->toBeFalse();

    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    expect(WrgroupOutbox::count())->toBe(0);
});

test('kasus DIBAYAR tidak lagi mengirim event invoice/payment — fee enumerator bukan dasar komisi KH', function () {
    wrgroupAktifUji();
    $dataLapangan = dataLapanganBelumBayarUji();

    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    // recordDataLapanganPaid() dimatikan (lihat WrgroupEventService): fee di sini adalah honor
    // enumerator/pendamping (Pengeluaran KH), bukan pemasukan KH. Dasar komisi sekarang dari
    // reportPendapatanBersih() (ledger Arus Kas), diuji terpisah di bawah.
    expect(WrgroupOutbox::count())->toBe(0);
});

test('deliver() menandai event sent pada respons 2xx', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['status' => 'ok', 'environment' => 'testing', 'persisted' => false], 200)]);

    app(WrgroupEventService::class)->reportPendapatanBersih('2020-Q1', 1000000);
    $row = WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->firstOrFail();
    app(WrgroupDeliveryService::class)->deliver($row);

    expect($row->fresh()->status)->toBe('sent')
        ->and($row->fresh()->environment)->toBe('testing')
        ->and($row->fresh()->persisted)->toBeFalse();
});

test('deliver() memperlakukan 409 sebagai sukses (duplikat)', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['status' => 'duplicate'], 409)]);

    app(WrgroupEventService::class)->reportPendapatanBersih('2020-Q1', 1000000);
    $row = WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->firstOrFail();
    app(WrgroupDeliveryService::class)->deliver($row);

    expect($row->fresh()->status)->toBe('sent');
});

test('deliver() menjadwalkan retry dengan backoff pada 422 retry:true', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['message' => 'invoice asal belum diterima', 'retry' => true], 422)]);

    app(WrgroupEventService::class)->reportPendapatanBersih('2020-Q1', 1000000);
    $row = WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->firstOrFail();
    app(WrgroupDeliveryService::class)->deliver($row);

    $fresh = $row->fresh();
    expect($fresh->status)->toBe('pending')
        ->and($fresh->attempts)->toBe(1)
        ->and($fresh->next_attempt_at)->not->toBeNull();
});

test('deliver() menolak permanen pada 422 tanpa retry — tidak dijadwalkan ulang', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['message' => 'data tidak valid'], 422)]);

    app(WrgroupEventService::class)->reportPendapatanBersih('2020-Q1', 1000000);
    $row = WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->firstOrFail();
    app(WrgroupDeliveryService::class)->deliver($row);

    expect($row->fresh()->status)->toBe('rejected')
        ->and($row->fresh()->isRetryable())->toBeTrue();
});

test('401 menghentikan pengiriman (circuit breaker) sampai resume() dipanggil', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['message' => 'unauthorized'], 401)]);

    app(WrgroupEventService::class)->reportPendapatanBersih('2020-Q1', 1000000);
    app(WrgroupEventService::class)->reportPendapatanBersih('2020-Q2', 2000000);

    $delivery = app(WrgroupDeliveryService::class);
    $row = WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->where('period', '2020-Q1')->firstOrFail();
    $delivery->deliver($row);

    expect($delivery->halted())->not->toBeNull()
        ->and($row->fresh()->status)->toBe('pending');

    // Event kedua tidak diproses sama sekali selama halted.
    $rowKedua = WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->where('period', '2020-Q2')->firstOrFail();
    $delivery->deliver($rowKedua);
    expect($rowKedua->fresh()->last_http_status)->toBeNull();

    $delivery->resume();
    expect($delivery->halted())->toBeNull();
});

test('heartbeat mengembalikan pesan tidak aktif ketika WRGROUP_ENABLED=false', function () {
    $result = app(WrgroupDeliveryService::class)->heartbeat();

    expect($result['ok'])->toBeFalse()
        ->and($result['http'])->toBeNull();
});

test('hasTransactions mendeteksi kasus lunas pada triwulan berjalan', function () {
    wrgroupAktifUji();
    $period = app(WrgroupEventService::class)->hasTransactions('2020-Q1');
    expect($period)->toBeFalse();

    $dataLapangan = dataLapanganBelumBayarUji();
    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR', 'updated_at' => '2020-02-15']);

    expect(app(WrgroupEventService::class)->hasTransactions('2020-Q1'))->toBeTrue();
});

test('reportNihil menolak triwulan yang masih ada transaksinya', function () {
    wrgroupAktifUji();
    $dataLapangan = dataLapanganBelumBayarUji();
    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR', 'updated_at' => '2020-02-15']);

    $result = app(WrgroupEventService::class)->reportNihil('2020-Q1');

    expect($result['ok'])->toBeFalse();
});

test('reportNihil mencatat baris outbox utk triwulan tanpa transaksi', function () {
    wrgroupAktifUji();

    $result = app(WrgroupEventService::class)->reportNihil('2019-Q1');

    expect($result['ok'])->toBeTrue()
        ->and(WrgroupOutbox::where('endpoint', 'nihil')->where('period', '2019-Q1')->exists())->toBeTrue();
});

test('reportPendapatanBersih mencatat baris outbox dengan nilai bersih ledger', function () {
    wrgroupAktifUji();

    $result = app(WrgroupEventService::class)->reportPendapatanBersih('2019-Q1', 3500000, 'Uji');

    expect($result['ok'])->toBeTrue();
    $row = WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->where('period', '2019-Q1')->first();
    expect($row)->not->toBeNull()
        ->and($row->transaction_id)->toBe('PB-2019-Q1')
        ->and($row->version)->toBe(1)
        ->and($row->payload)->toBe(['periode' => '2019-Q1', 'nilai' => 3500000.0, 'catatan' => 'Uji']);
});

test('reportPendapatanBersih mengirim ulang sebagai versi baru bila nilainya berubah, tidak menggandakan bila sama', function () {
    wrgroupAktifUji();

    app(WrgroupEventService::class)->reportPendapatanBersih('2019-Q1', 1000000);
    $ulang = app(WrgroupEventService::class)->reportPendapatanBersih('2019-Q1', 1000000);
    expect(WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->count())->toBe(1);
    expect($ulang['ok'])->toBeTrue();

    $koreksi = app(WrgroupEventService::class)->reportPendapatanBersih('2019-Q1', 1500000);
    expect($koreksi['ok'])->toBeTrue();
    expect(WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->count())->toBe(2);
    expect(WrgroupOutbox::where('endpoint', 'pendapatan_bersih')->orderByDesc('version')->first()->payload['nilai'])->toBe(1500000.0);
});

test('reportPendapatanBersih menolak triwulan yang belum dimulai dan format periode salah', function () {
    wrgroupAktifUji();

    expect(app(WrgroupEventService::class)->reportPendapatanBersih('2099-Q1', 1000000)['ok'])->toBeFalse();
    expect(app(WrgroupEventService::class)->reportPendapatanBersih('bukan-periode', 1000000)['ok'])->toBeFalse();
});

test('CashflowService::netPeriode menjumlahkan Pemasukan dikurangi Pengeluaran, mengecualikan tipe Kas', function () {
    Cashflow::create(['tipe' => 'Pemasukan', 'jumlah' => 5000000, 'keterangan' => 'Masuk 1', 'tanggal' => '2019-02-10']);
    Cashflow::create(['tipe' => 'Pemasukan', 'jumlah' => 1000000, 'keterangan' => 'Masuk 2', 'tanggal' => '2019-03-01']);
    Cashflow::create(['tipe' => 'Pengeluaran', 'jumlah' => 2000000, 'keterangan' => 'Keluar 1', 'tanggal' => '2019-02-20']);
    Cashflow::create(['tipe' => 'Kas', 'jumlah' => 9999999, 'keterangan' => 'Penyesuaian kas', 'tanggal' => '2019-02-15']);
    Cashflow::create(['tipe' => 'Pemasukan', 'jumlah' => 7000000, 'keterangan' => 'Di luar rentang', 'tanggal' => '2019-06-01']);

    $net = app(CashflowService::class)->netPeriode(
        \Illuminate\Support\Carbon::parse('2019-01-01'),
        \Illuminate\Support\Carbon::parse('2019-03-31'),
    );

    expect($net)->toBe(4000000.0);
});

function setoranKomisiTerkirimUji(string $eventId, float $jumlah = 750000): WrgroupKomisiPembayaran
{
    return WrgroupKomisiPembayaran::create([
        'event_id' => $eventId, 'komisi_reference' => 'KOMISI-REF-1', 'periode' => '2019-Q1',
        'jumlah' => $jumlah, 'tanggal_transaksi_bank' => '2019-04-10', 'referensi_bank' => 'TRX-'.$eventId,
        'bukti_setoran' => 'wrgroup/komisi-bukti-setoran/x.pdf', 'berhasil' => true, 'http_status' => 200,
        'dikirim_oleh' => User::factory()->create()->id,
    ]);
}

function responsKomisiPeriodeUji(array $pembayaran): array
{
    return ['status' => 'ok', 'environment' => 'production', 'data' => [[
        'id' => 'KOMISI-REF-1', 'periode' => '2019-Q1', 'status' => 'dibayar_sebagian', 'pembayaran' => $pembayaran,
    ]]];
}

test('sinkronVerifikasi membukukan setoran terverifikasi sekali sebagai Pengeluaran, yang ditolak tidak', function () {
    wrgroupAktifUji();
    $ok = setoranKomisiTerkirimUji('evt-ok');
    $tolak = setoranKomisiTerkirimUji('evt-tolak');
    $menunggu = setoranKomisiTerkirimUji('evt-menunggu');

    Http::fake(['wrgroup.test/*' => Http::response(responsKomisiPeriodeUji([
        ['event_id' => 'evt-ok', 'status' => 'terverifikasi', 'diverifikasi_at' => '2019-04-12T10:00:00+07:00', 'catatan' => null],
        ['event_id' => 'evt-tolak', 'status' => 'ditolak', 'diverifikasi_at' => '2019-04-12T10:00:00+07:00', 'catatan' => 'Nominal tidak sesuai'],
        ['event_id' => 'evt-menunggu', 'status' => 'menunggu_verifikasi', 'diverifikasi_at' => null, 'catatan' => null],
    ]), 200)]);

    $service = app(\App\Services\Integrasi\Wrgroup\WrgroupKomisiService::class);
    $hasil = $service->sinkronVerifikasi();

    expect($hasil['ok'])->toBeTrue()->and($hasil['dibukukan'])->toBe(1);

    $cashflow = Cashflow::where('sumber', Cashflow::SUMBER_KOMISI_WRGROUP)->sole();
    expect($cashflow->tipe)->toBe('Pengeluaran')
        ->and((float) $cashflow->jumlah)->toBe(750000.0)
        ->and(\Illuminate\Support\Carbon::parse($cashflow->tanggal)->toDateString())->toBe('2019-04-10')
        ->and($ok->fresh()->cashflow_id)->toBe($cashflow->id)
        ->and($ok->fresh()->dibukukan_at)->not->toBeNull()
        ->and($tolak->fresh()->status_verifikasi)->toBe('ditolak')
        ->and($tolak->fresh()->catatan_verifikasi)->toBe('Nominal tidak sesuai')
        ->and($tolak->fresh()->cashflow_id)->toBeNull()
        ->and($menunggu->fresh()->status_verifikasi)->toBe('menunggu_verifikasi');

    // Sinkron ulang tidak membukukan dua kali.
    $service->sinkronVerifikasi();
    expect(Cashflow::where('sumber', Cashflow::SUMBER_KOMISI_WRGROUP)->count())->toBe(1);
});

test('setoran yang dibukukan tidak dibukukan ulang walau entri Arus Kasnya dihapus manual', function () {
    wrgroupAktifUji();
    $row = setoranKomisiTerkirimUji('evt-hapus');
    Http::fake(['wrgroup.test/*' => Http::response(responsKomisiPeriodeUji([
        ['event_id' => 'evt-hapus', 'status' => 'terverifikasi', 'diverifikasi_at' => '2019-04-12T10:00:00+07:00', 'catatan' => null],
    ]), 200)]);

    $service = app(\App\Services\Integrasi\Wrgroup\WrgroupKomisiService::class);
    $service->sinkronVerifikasi();
    $row->fresh()->cashflow->delete();

    $service->sinkronVerifikasi();
    expect(Cashflow::where('sumber', Cashflow::SUMBER_KOMISI_WRGROUP)->count())->toBe(0);
});

test('sinkronVerifikasi tidak memanggil WRGROUP bila tidak ada setoran yang menunggu', function () {
    wrgroupAktifUji();
    Http::fake();

    $hasil = app(\App\Services\Integrasi\Wrgroup\WrgroupKomisiService::class)->sinkronVerifikasi();

    expect($hasil['ok'])->toBeTrue();
    Http::assertNothingSent();
});

test('setoran komisi terverifikasi tidak ikut mengurangi dasar komisi (netPeriode)', function () {
    Cashflow::create(['tipe' => 'Pemasukan', 'jumlah' => 5000000, 'keterangan' => 'Masuk', 'tanggal' => '2019-05-10']);
    Cashflow::create(['tipe' => 'Pengeluaran', 'jumlah' => 1000000, 'keterangan' => 'Operasional', 'tanggal' => '2019-05-11']);
    Cashflow::create(['tipe' => 'Pengeluaran', 'sumber' => Cashflow::SUMBER_KOMISI_WRGROUP, 'jumlah' => 400000, 'keterangan' => 'Setoran komisi', 'tanggal' => '2019-05-12']);

    $net = app(CashflowService::class)->netPeriode(\Illuminate\Support\Carbon::parse('2019-04-01'), \Illuminate\Support\Carbon::parse('2019-06-30'));

    expect($net)->toBe(4000000.0);
});

test('pengajuan pembayaran komisi mengirim multipart dan mencatat hasil', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['status' => 'ok'], 200)]);

    $user = User::factory()->create();
    $bukti = UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf');

    $row = app(\App\Services\Integrasi\Wrgroup\WrgroupKomisiService::class)->ajukanPembayaran(
        'KOMISI-REF-1',
        '2026-Q1',
        ['jumlah' => 500000, 'tanggal_transaksi_bank' => '2026-04-01', 'referensi_bank' => 'TRX123'],
        $bukti,
        $user,
    );

    expect($row->berhasil)->toBeTrue()
        ->and($row->komisi_reference)->toBe('KOMISI-REF-1')
        ->and($row->dikirim_oleh)->toBe($user->id)
        ->and(WrgroupKomisiPembayaran::count())->toBe(1);
});
