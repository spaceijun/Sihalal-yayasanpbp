<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ubah kolom 'pengalaman' dari VARCHAR(255) ke TEXT
 * agar bisa menampung teks pengalaman kerja yang panjang.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->text('pengalaman')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->string('pengalaman')->nullable()->change();
        });
    }
};
