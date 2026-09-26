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
            $table->decimal('latitude', 10, 7)->nullable()->after('kode_pos');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('akurasi_meter', 8, 2)->nullable()->after('longitude');
            $table->timestamp('geotag_captured_at')->nullable()->after('akurasi_meter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'akurasi_meter', 'geotag_captured_at']);
        });
    }
};
