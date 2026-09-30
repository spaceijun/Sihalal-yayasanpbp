<?php

use App\Models\Superadmin\WrgroupSurat;
use App\Models\User;
use App\Services\Integrasi\Wrgroup\WrgroupSuratService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

function suratWrgroupAktifUji(): void
{
    config([
        'wrgroup.enabled' => true,
        'wrgroup.business_id' => 'BIZ-TEST',
        'wrgroup.api_key' => 'key-test',
        'wrgroup.api_secret' => 'secret-test',
        'wrgroup.api_url' => 'https://wrgroup.test/api/v1',
    ]);
}

test('ajukan berhasil mencatat baris lokal berstatus diajukan dan mengirim data pengaju', function () {
    suratWrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response([
        'status' => 'processed', 'reference' => 'abc-123', 'status_surat' => 'diajukan',
        'environment' => 'production', 'persisted' => true,
    ], 200)]);

    $user = User::factory()->create();
    $row = app(WrgroupSuratService::class)->ajukan([
        'surat_jenis_kode' => 'SK', 'jenis_nama' => 'Surat Keterangan', 'perihal' => 'Uji Surat',
        'data_variabel' => ['penerima' => 'Budi'],
    ], $user);

    expect($row->berhasil_kirim)->toBeTrue()
        ->and($row->wrgroup_hashed_id)->toBe('abc-123')
        ->and($row->status)->toBe('diajukan')
        ->and($row->perihal)->toBe('Uji Surat');

    Http::assertSent(fn ($request) => $request->url() === 'https://wrgroup.test/api/v1/surat'
        && $request['surat_jenis_kode'] === 'SK'
        && $request['pengaju_nama'] === $user->name);
});

test('ajukan gagal mencatat pesan kesalahan, dan ulangi bisa mengirim ulang sampai berhasil', function () {
    suratWrgroupAktifUji();
    Http::fake(['wrgroup.test/*' => Http::response(['status' => 'rejected', 'message' => 'Jenis surat ini tidak dibuka untuk pengajuan pilar.'], 422)]);

    $user = User::factory()->create();
    $service = app(WrgroupSuratService::class);
    $row = $service->ajukan(['surat_jenis_kode' => 'SK', 'perihal' => 'Uji Gagal'], $user);

    expect($row->berhasil_kirim)->toBeFalse()
        ->and($row->wrgroup_hashed_id)->toBeNull()
        ->and($row->response_message)->toContain('tidak dibuka');

    Http::fake(['wrgroup.test/*' => Http::response(['status' => 'processed', 'reference' => 'xyz-999', 'status_surat' => 'diajukan'], 200)]);
    $row = $service->ulangi($row->fresh());

    expect($row->berhasil_kirim)->toBeTrue()->and($row->wrgroup_hashed_id)->toBe('xyz-999');
});

test('sinkronStatus memperbarui status dan mengunduh PDF sekali saat pertama kali terbit', function () {
    Storage::fake('public');
    suratWrgroupAktifUji();
    $user = User::factory()->create();
    $row = WrgroupSurat::create([
        'wrgroup_hashed_id' => 'sudah-terkirim', 'surat_jenis_kode' => 'SK', 'jenis_nama' => 'SK',
        'perihal' => 'Uji Sinkron', 'status' => 'diperiksa', 'berhasil_kirim' => true, 'diajukan_oleh' => $user->id,
    ]);

    Http::fake([
        'wrgroup.test/api/v1/surat/sudah-terkirim' => Http::response(['status' => 'ok', 'data' => [
            'hashed_id' => 'sudah-terkirim', 'status' => 'terbit', 'nomor' => '001/SK/PST/I/2026',
            'diterbitkan_at' => '2026-01-15T10:00:00+07:00', 'pdf_tersedia' => true,
        ]], 200),
        'wrgroup.test/api/v1/surat/sudah-terkirim/pdf' => Http::response('%PDF-1.4 isi-pdf-uji', 200),
    ]);

    $jumlah = app(WrgroupSuratService::class)->sinkronStatus();

    expect($jumlah)->toBe(1);
    $row = $row->fresh();
    expect($row->status)->toBe('terbit')
        ->and($row->nomor)->toBe('001/SK/PST/I/2026')
        ->and($row->pdf_path)->not->toBeNull();
    Storage::disk('public')->assertExists($row->pdf_path);
});

test('sinkronStatus tidak menyentuh surat yang sudah berstatus final', function () {
    suratWrgroupAktifUji();
    Http::fake();
    $user = User::factory()->create();
    WrgroupSurat::create([
        'wrgroup_hashed_id' => 'sudah-terbit', 'surat_jenis_kode' => 'SK', 'jenis_nama' => 'SK',
        'perihal' => 'X', 'status' => 'terbit', 'berhasil_kirim' => true, 'diajukan_oleh' => $user->id,
    ]);

    $jumlah = app(WrgroupSuratService::class)->sinkronStatus();

    expect($jumlah)->toBe(0);
    Http::assertNothingSent();
});
