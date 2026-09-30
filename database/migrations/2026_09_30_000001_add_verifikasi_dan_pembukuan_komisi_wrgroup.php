<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pembukuan setoran komisi WRGROUP ke Arus Kas setelah WRGROUP memverifikasinya
 * (WrgroupKomisiService::sinkronVerifikasi()) — lihat .agent/workflows/wrgroup-integrasi.md § Komisi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashflows', function (Blueprint $table) {
            $table->string('sumber', 30)->nullable()->after('tipe')
                ->comment('Null = entri manual/biasa; komisi_wrgroup = setoran komisi terverifikasi (dikecualikan dari dasar komisi)');
        });

        Schema::table('wrgroup_komisi_pembayarans', function (Blueprint $table) {
            $table->string('status_verifikasi', 30)->nullable()->after('response_message')
                ->comment('Status di WRGROUP: menunggu_verifikasi | terverifikasi | ditolak (null = belum tersinkron)');
            $table->timestamp('diverifikasi_at')->nullable()->after('status_verifikasi');
            $table->text('catatan_verifikasi')->nullable()->after('diverifikasi_at');
            $table->foreignId('cashflow_id')->nullable()->after('catatan_verifikasi')
                ->constrained('cashflows')->nullOnDelete();
            $table->timestamp('dibukukan_at')->nullable()->after('cashflow_id')
                ->comment('Sekali terisi tidak dibukukan ulang, walau entri Arus Kasnya dihapus manual');
        });
    }

    public function down(): void
    {
        Schema::table('wrgroup_komisi_pembayarans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cashflow_id');
            $table->dropColumn(['status_verifikasi', 'diverifikasi_at', 'catatan_verifikasi', 'dibukukan_at']);
        });

        Schema::table('cashflows', function (Blueprint $table) {
            $table->dropColumn('sumber');
        });
    }
};
