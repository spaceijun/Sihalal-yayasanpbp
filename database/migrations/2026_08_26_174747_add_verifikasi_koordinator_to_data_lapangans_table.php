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
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->enum('verifikasi_koordinator', ['Belum', 'Terverifikasi', 'Perlu Koreksi'])
                ->default('Belum')
                ->after('status');
            $table->text('catatan_koordinator')->nullable()->after('verifikasi_koordinator');
            $table->timestamp('verified_at_koordinator')->nullable()->after('catatan_koordinator');
            $table->unsignedBigInteger('verified_by_koordinator')->nullable()->after('verified_at_koordinator');
            $table->foreign('verified_by_koordinator')->references('id')->on('koordinators')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropForeign(['verified_by_koordinator']);
            $table->dropColumn([
                'verifikasi_koordinator',
                'catatan_koordinator',
                'verified_at_koordinator',
                'verified_by_koordinator',
            ]);
        });
    }
};
