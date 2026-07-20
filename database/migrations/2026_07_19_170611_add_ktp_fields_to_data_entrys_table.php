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
            $table->string('nik', 20)->nullable()->after('alamat');
            $table->string('nama_lengkap_ktp')->nullable()->after('nik');
            $table->string('foto_ktp')->nullable()->after('nama_lengkap_ktp');
            $table->enum('pendidikan_terakhir', [
                'Tamat SD',
                'Tamat SMP/MTS',
                'Tamat SMA/SMK/MA',
                'Tamat D1/D2',
                'Tamat D3/S1',
                'Tamat S2',
                'Tamat S3',
            ])->nullable()->after('foto_ktp');
            $table->string('foto_ijasah')->nullable()->after('pendidikan_terakhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_entrys', function (Blueprint $table) {
            $table->dropColumn(['nik', 'nama_lengkap_ktp', 'foto_ktp', 'pendidikan_terakhir', 'foto_ijasah']);
        });
    }
};
