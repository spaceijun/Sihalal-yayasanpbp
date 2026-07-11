<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom keterangan_pembayaran (jika belum ada)
        if (! Schema::hasColumn('data_lapangans', 'keterangan_pembayaran')) {
            Schema::table('data_lapangans', function (Blueprint $table) {
                $table->string('keterangan_pembayaran')->nullable()->after('status_pembayaran');
            });
        }

        // 2. Update data PENDING → PENGAJUAN dulu (masih dalam enum lama PENDING,PENGAJUAN,DIBAYAR)
        //    agar saat ALTER TABLE tidak ada nilai 'PENDING' yang tidak dikenal
        DB::statement("UPDATE data_lapangans SET status_pembayaran = 'PENGAJUAN' WHERE status_pembayaran = 'PENDING'");

        // 3. Sekarang ubah enum (tidak ada sisa nilai 'PENDING')
        DB::statement("ALTER TABLE data_lapangans MODIFY COLUMN status_pembayaran ENUM('TIDAK ADA PENGAJUAN','PENGAJUAN','DIBAYAR','DITOLAK') NOT NULL DEFAULT 'TIDAK ADA PENGAJUAN'");

        // 4. Reset semua data yang baru saja di-set PENGAJUAN (dari PENDING) dan statusnya bukan TERBIT SH
        //    → TIDAK ADA PENGAJUAN (belum layak diajukan)
        //    Data yang sudah TERBIT SH dan sebelumnya PENDING (kini PENGAJUAN) → juga reset ke
        //    TIDAK ADA PENGAJUAN sesuai keputusan: enumerator ajukan mandiri ulang lewat Flutter
        //
        //    Catatan: Kita tidak bisa tahu mana yang "benar-benar" sudah diajukan oleh admin umum
        //    vs yang auto-PENDING, jadi semua yang dari PENDING → TIDAK ADA PENGAJUAN.
        //    Yang sudah PENGAJUAN sejak awal (bukan dari PENDING) → dibiarkan tetap.
        //
        //    Cara membedakan: karena kita baru saja set semua PENDING → PENGAJUAN,
        //    kita tidak bisa membedakan. Namun sesuai keputusan, semua data "lama" yang
        //    sebelumnya PENDING harus di-reset. Karena data yang benar-benar PENGAJUAN (dari admin umum)
        //    kemungkinan lebih sedikit, dan sistem baru ini menggantikan seluruh alur lama,
        //    kita reset SEMUA menjadi TIDAK ADA PENGAJUAN lalu enumerator ajukan ulang.
        //
        //    NAMUN: kita tidak ingin reset yang sudah DIBAYAR (aman, tidak tersentuh).
        //    Yang kita ubah hanya yang status_pembayaran = PENGAJUAN (hasil dari langkah 2).
        DB::statement("UPDATE data_lapangans SET status_pembayaran = 'TIDAK ADA PENGAJUAN' WHERE status_pembayaran = 'PENGAJUAN'");
    }

    public function down(): void
    {
        // Rollback: kembalikan TIDAK ADA PENGAJUAN & DITOLAK ke PENDING
        DB::statement("UPDATE data_lapangans SET status_pembayaran = 'PENGAJUAN' WHERE status_pembayaran IN ('TIDAK ADA PENGAJUAN', 'DITOLAK')");

        // Kembalikan enum lama
        DB::statement("ALTER TABLE data_lapangans MODIFY COLUMN status_pembayaran ENUM('PENDING','PENGAJUAN','DIBAYAR') NOT NULL DEFAULT 'PENDING'");

        // Ubah PENGAJUAN kembali ke PENDING
        DB::statement("UPDATE data_lapangans SET status_pembayaran = 'PENDING' WHERE status_pembayaran = 'PENGAJUAN'");

        // Hapus kolom keterangan_pembayaran
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn('keterangan_pembayaran');
        });
    }
};
