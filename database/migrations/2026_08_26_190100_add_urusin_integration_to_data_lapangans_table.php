<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom baru untuk mendukung .agent/workflows/data-entry-integrasi.md:
     *   - Verifikasi final Admin Umum/Superadmin (tahap 2, terpisah dari `status`/`TERVERIFIKASI`
     *     lama supaya alur lama & baru bisa berjalan berdampingan — lihat §1.1 & §5.1 dokumen).
     *   - Penanda jalur data entry per-record ('lama' = manual role data_entry, 'baru' = via
     *     Urusin Secara Online).
     *   - Field tambahan yang dibutuhkan payload API Urusin Secara Online tapi belum ada
     *     kolomnya di data_lapangans (lihat §4 dokumen) — diisi manual oleh Admin Umum/Superadmin
     *     di form "Data Usaha untuk Pengajuan" sebelum verifikasi final disetujui.
     *   - Tracking submission NIB & Halal ke Urusin Secara Online.
     */
    public function up(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            // Verifikasi final (tahap 2) — mirror verifikasi_koordinator, tapi field terpisah
            $table->enum('verifikasi_final', ['Belum', 'Terverifikasi', 'Perlu Koreksi'])
                ->default('Belum')
                ->after('verified_by_koordinator');
            $table->text('catatan_final')->nullable()->after('verifikasi_final');
            $table->timestamp('verified_at_final')->nullable()->after('catatan_final');
            $table->unsignedBigInteger('verified_by_final')->nullable()->after('verified_at_final');
            $table->foreign('verified_by_final')->references('id')->on('users')->nullOnDelete();

            // Penanda jalur — default 'lama' untuk semua data existing; diset 'baru' oleh
            // aplikasi hanya saat verifikasi final (aksi baru) benar-benar dipakai.
            $table->string('jalur_data_entry', 10)->default('lama')->after('verified_by_final');

            // Data usaha tambahan untuk payload NIB & Halal (§4/§5.2 data-entry-integrasi.md)
            $table->string('nama_usaha')->nullable()->after('jalur_data_entry');
            $table->string('tempat_lahir')->nullable()->after('nama_usaha');
            $table->string('jenis_usaha')->nullable()->after('tempat_lahir');
            $table->unsignedBigInteger('modal_usaha')->nullable()->after('jenis_usaha');
            $table->string('alamat_usaha')->nullable()->after('modal_usaha');
            $table->string('provinsi_kode', 10)->nullable()->after('alamat_usaha');
            $table->string('kabupaten_kode', 15)->nullable()->after('provinsi_kode');
            $table->string('kecamatan_kode', 15)->nullable()->after('kabupaten_kode');
            $table->string('kelurahan_kode', 20)->nullable()->after('kecamatan_kode');
            // Berlaku untuk semua produk (nama_produk..nama_produk_5) — lihat catatan di §4:
            // API mensyaratkan jenis_produk & bahan_utama per produk, tapi kolom data_lapangans
            // hanya punya satu set field usaha untuk semua produknya sekaligus.
            $table->string('jenis_produk_halal')->nullable()->after('kelurahan_kode');
            $table->string('bahan_utama_halal')->nullable()->after('jenis_produk_halal');

            // Tracking submission ke Urusin Secara Online
            $table->string('urusin_nib_submission_id')->nullable()->after('bahan_utama_halal');
            $table->string('urusin_nib_status')->nullable()->after('urusin_nib_submission_id');
            $table->string('urusin_nib_document_path')->nullable()->after('urusin_nib_status');
            $table->string('urusin_halal_submission_id')->nullable()->after('urusin_nib_document_path');
            $table->string('urusin_halal_status')->nullable()->after('urusin_halal_submission_id');
            $table->string('urusin_halal_document_path')->nullable()->after('urusin_halal_status');
            $table->timestamp('urusin_last_synced_at')->nullable()->after('urusin_halal_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropForeign(['verified_by_final']);
            $table->dropColumn([
                'verifikasi_final',
                'catatan_final',
                'verified_at_final',
                'verified_by_final',
                'jalur_data_entry',
                'nama_usaha',
                'tempat_lahir',
                'jenis_usaha',
                'modal_usaha',
                'alamat_usaha',
                'provinsi_kode',
                'kabupaten_kode',
                'kecamatan_kode',
                'kelurahan_kode',
                'jenis_produk_halal',
                'bahan_utama_halal',
                'urusin_nib_submission_id',
                'urusin_nib_status',
                'urusin_nib_document_path',
                'urusin_halal_submission_id',
                'urusin_halal_status',
                'urusin_halal_document_path',
                'urusin_last_synced_at',
            ]);
        });
    }
};
