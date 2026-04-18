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
        Schema::table('data_entrys', function (Blueprint $table) {
            $table->foreignId('bank_id')->after('entry_type')->nullable()->constrained('data_banks')->onDelete('cascade');
            $table->string('no_rekening')->after('bank_id')->nullable();
            $table->string('nama_rekening')->after('no_rekening')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_entrys', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');
            $table->dropColumn('no_rekening');
            $table->dropColumn('nama_rekening');
        });
    }
};
