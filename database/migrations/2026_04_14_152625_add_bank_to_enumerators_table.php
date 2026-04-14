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
        Schema::table('enumerators', function (Blueprint $table) {
            $table->foreignId('bank_id')->nullable()->after('status')->constrained('data_banks')->onDelete('cascade');
            $table->string('no_rekening')->nullable()->after('bank_id');
            $table->string('nama_rekening')->nullable()->after('no_rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enumerators', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn(['bank_id', 'no_rekening', 'nama_rekening']);
        });
    }
};
