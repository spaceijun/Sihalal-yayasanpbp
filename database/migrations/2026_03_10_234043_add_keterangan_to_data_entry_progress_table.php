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
            $table->text('keterangan_revisi')->after('new_data')->nullable();
            $table->text('keterangan_update')->after('keterangan_revisi')->nullable();
            $table->enum('status', ['DITERIMA', 'REVISI', 'DITOLAK', 'PENDING'])->default('PENDING')->after('keterangan_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_entry_progress', function (Blueprint $table) {
            $table->dropColumn('keterangan_revisi');
            $table->dropColumn('keterangan_update');
            $table->dropColumn('status');
        });
    }
};
