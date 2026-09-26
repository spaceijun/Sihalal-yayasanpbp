<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('koordinators', function (Blueprint $table) {
            // Foto
            $table->string('foto_ktp')->nullable()->after('alamat');
            $table->string('foto_formal')->nullable()->after('foto_ktp');

            // Alamat detail (menggantikan kolom 'alamat' lama, di-rename di bawah)
            $table->string('provinsi_ktp')->nullable()->after('foto_formal');
            $table->string('kabupaten_ktp')->nullable()->after('provinsi_ktp');
            $table->string('kecamatan_ktp')->nullable()->after('kabupaten_ktp');
            $table->string('desa_ktp')->nullable()->after('kecamatan_ktp');
            $table->string('rt_ktp', 10)->nullable()->after('desa_ktp');
            $table->string('rw_ktp', 10)->nullable()->after('rt_ktp');

            // Wilayah Kerja
            $table->enum('tipe_wilayah_kerja', ['Provinsi', 'Kabupaten'])->nullable()->after('rw_ktp');
            $table->string('provinsi_kerja')->nullable()->after('tipe_wilayah_kerja');
            $table->string('kabupaten_kerja')->nullable()->after('provinsi_kerja');

            // Informasi Lainnya
            $table->date('tanggal_mulai')->nullable()->after('kabupaten_kerja');
        });

        // Rename kolom 'alamat' lama -> 'alamat_lengkap_ktp'
        Schema::table('koordinators', function (Blueprint $table) {
            $table->renameColumn('alamat', 'alamat_lengkap_ktp');
        });

        // Fee dikelola via modul Fee Enumerator terpisah (skala Global/Provinsi)
        Schema::table('koordinators', function (Blueprint $table) {
            $table->dropColumn('fee_enum');
        });

        // Tambah opsi 'Blacklist' pada enum status
        DB::statement("ALTER TABLE koordinators MODIFY COLUMN status ENUM('Aktif', 'Tidak Aktif', 'Blacklist') NOT NULL DEFAULT 'Aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE koordinators MODIFY COLUMN status ENUM('Aktif', 'Tidak Aktif') NOT NULL DEFAULT 'Aktif'");

        Schema::table('koordinators', function (Blueprint $table) {
            $table->decimal('fee_enum', 15, 2)->nullable()->after('telephone');
        });

        Schema::table('koordinators', function (Blueprint $table) {
            $table->renameColumn('alamat_lengkap_ktp', 'alamat');
        });

        Schema::table('koordinators', function (Blueprint $table) {
            $table->dropColumn([
                'foto_ktp',
                'foto_formal',
                'provinsi_ktp',
                'kabupaten_ktp',
                'kecamatan_ktp',
                'desa_ktp',
                'rt_ktp',
                'rw_ktp',
                'tipe_wilayah_kerja',
                'provinsi_kerja',
                'kabupaten_kerja',
                'tanggal_mulai',
            ]);
        });
    }
};
