<?php

use App\Models\DataLapangan;
use App\Models\Enumerator;
use App\Models\Superadmin\Koordinator;
use App\Models\Superadmin\WrgroupKomisiPembayaran;
use App\Models\Superadmin\WrgroupOutbox;
use App\Models\User;
use App\Services\Integrasi\Wrgroup\WrgroupDeliveryService;
use App\Services\Integrasi\Wrgroup\WrgroupEventService;
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

test('kasus DIBAYAR dengan WRGROUP_ENABLED=true mencatat event invoice dan payment', function () {
    wrgroupAktifUji();
    $dataLapangan = dataLapanganBelumBayarUji();

    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    expect(WrgroupOutbox::count())->toBe(2)
        ->and(WrgroupOutbox::where('endpoint', 'invoice')->count())->toBe(1)
        ->and(WrgroupOutbox::where('endpoint', 'payment')->count())->toBe(1);

    $invoice = WrgroupOutbox::where('endpoint', 'invoice')->first();
    expect($invoice->transaction_id)->toBe($dataLapangan->no_registrasi)
        ->and($invoice->payload['data']['nilai_dasar'])->toBe(DataLapangan::resolveFee($dataLapangan->fresh()))
        ->and($invoice->status)->toBe('pending');
});

test('update lain pada kasus yang sudah DIBAYAR tidak mencatat versi baru bila fee tidak berubah', function () {
    wrgroupAktifUji();
    $dataLapangan = dataLapanganBelumBayarUji();
    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    expect(WrgroupOutbox::count())->toBe(2);

    // Perubahan field tak terkait — observer tidak terpicu ulang (wasChanged status_pembayaran = false).
    $dataLapangan->update(['keterangan' => 'catatan tambahan']);

    expect(WrgroupOutbox::count())->toBe(2);
});

test('deliver() menandai event sent pada respons 2xx', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['status' => 'ok', 'environment' => 'testing', 'persisted' => false], 200)]);

    $dataLapangan = dataLapanganBelumBayarUji();
    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    $row = WrgroupOutbox::where('endpoint', 'invoice')->firstOrFail();
    app(WrgroupDeliveryService::class)->deliver($row);

    expect($row->fresh()->status)->toBe('sent')
        ->and($row->fresh()->environment)->toBe('testing')
        ->and($row->fresh()->persisted)->toBeFalse();
});

test('deliver() memperlakukan 409 sebagai sukses (duplikat)', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['status' => 'duplicate'], 409)]);

    $dataLapangan = dataLapanganBelumBayarUji();
    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    $row = WrgroupOutbox::where('endpoint', 'invoice')->firstOrFail();
    app(WrgroupDeliveryService::class)->deliver($row);

    expect($row->fresh()->status)->toBe('sent');
});

test('deliver() menjadwalkan retry dengan backoff pada 422 retry:true', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['message' => 'invoice asal belum diterima', 'retry' => true], 422)]);

    $dataLapangan = dataLapanganBelumBayarUji();
    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    $row = WrgroupOutbox::where('endpoint', 'invoice')->firstOrFail();
    app(WrgroupDeliveryService::class)->deliver($row);

    $fresh = $row->fresh();
    expect($fresh->status)->toBe('pending')
        ->and($fresh->attempts)->toBe(1)
        ->and($fresh->next_attempt_at)->not->toBeNull();
});

test('deliver() menolak permanen pada 422 tanpa retry — tidak dijadwalkan ulang', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['message' => 'data tidak valid'], 422)]);

    $dataLapangan = dataLapanganBelumBayarUji();
    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    $row = WrgroupOutbox::where('endpoint', 'invoice')->firstOrFail();
    app(WrgroupDeliveryService::class)->deliver($row);

    expect($row->fresh()->status)->toBe('rejected')
        ->and($row->fresh()->isRetryable())->toBeTrue();
});

test('401 menghentikan pengiriman (circuit breaker) sampai resume() dipanggil', function () {
    wrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['message' => 'unauthorized'], 401)]);

    $dataLapangan = dataLapanganBelumBayarUji();
    $dataLapangan->update(['status_pembayaran' => 'DIBAYAR']);

    $delivery = app(WrgroupDeliveryService::class);
    $row = WrgroupOutbox::where('endpoint', 'invoice')->firstOrFail();
    $delivery->deliver($row);

    expect($delivery->halted())->not->toBeNull()
        ->and($row->fresh()->status)->toBe('pending');

    // Event kedua tidak diproses sama sekali selama halted.
    $rowPayment = WrgroupOutbox::where('endpoint', 'payment')->firstOrFail();
    $delivery->deliver($rowPayment);
    expect($rowPayment->fresh()->last_http_status)->toBeNull();

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
