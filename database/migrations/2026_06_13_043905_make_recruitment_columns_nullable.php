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
        Schema::table('recruitments', function (Blueprint $table) {
            $table->string('foto_diri')->nullable()->change();
            $table->string('foto_ktp')->nullable()->change();
            $table->string('foto_ijasah')->nullable()->change();
            $table->string('pakta_integritas')->nullable()->change();
            $table->string('nik')->nullable()->change();
            $table->string('jenis_kelamin')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->string('foto_diri')->nullable(false)->change();
            $table->string('foto_ktp')->nullable(false)->change();
            $table->string('foto_ijasah')->nullable(false)->change();
            $table->string('pakta_integritas')->nullable(false)->change();
            $table->string('nik')->nullable(false)->change();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable(false)->change();
        });
    }
};
