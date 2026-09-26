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
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('kategori', ['Data Lapangan', 'Enumerator', 'Teknis Lapangan', 'Lainnya'])
                ->default('Lainnya')
                ->after('subject');
            $table->unsignedBigInteger('ref_data_lapangan_id')->nullable()->after('kategori');
            $table->foreign('ref_data_lapangan_id')->references('id')->on('data_lapangans')->nullOnDelete();
            $table->unsignedBigInteger('ref_enumerator_id')->nullable()->after('ref_data_lapangan_id');
            $table->foreign('ref_enumerator_id')->references('id')->on('enumerators')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['ref_data_lapangan_id']);
            $table->dropForeign(['ref_enumerator_id']);
            $table->dropColumn(['kategori', 'ref_data_lapangan_id', 'ref_enumerator_id']);
        });
    }
};
