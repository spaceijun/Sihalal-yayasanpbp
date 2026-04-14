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
            $table->string('nik')->unique()->after('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->after('nik');
            $table->string('foto_ijasah')->after('foto_ktp');
            $table->string('pakta_integritas')->after('foto_ijasah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->dropColumn(['nik', 'jenis_kelamin', 'foto_ijasah', 'pakta_integritas']);
        });
    }
};
