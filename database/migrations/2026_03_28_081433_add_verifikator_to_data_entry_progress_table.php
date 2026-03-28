<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('data_entry_progress', function (Blueprint $table) {
            $table->foreignId('verifikator_id')
                ->nullable()
                ->constrained('verifikators')
                ->nullOnDelete()
                ->after('data_lapangan_id');

            $table->date('tanggal_verifikasi')
                ->nullable()
                ->after('verifikator_id');

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained('verifikator_payments')
                ->nullOnDelete()
                ->after('tanggal_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('data_entry_progress', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verifikator_id');
            $table->dropConstrainedForeignId('payment_id');
            $table->dropColumn(['tanggal_verifikasi']);
        });
    }
};
