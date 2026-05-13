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
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->string('no_registrasi')->nullable()->unique()->after('id');
        });

        // Auto-fill data lama berdasarkan urutan id
        $rows = DB::table('data_lapangans')->orderBy('id')->get();
        foreach ($rows as $index => $row) {
            $urutan = str_pad($index + 1, 5, '0', STR_PAD_LEFT);
            $tahun = date('Y', strtotime($row->created_at));

            DB::table('data_lapangans')->where('id', $row->id)->update([
                'no_registrasi' => 'KH' . $tahun . '-' . $urutan,
            ]);
        }

        // Setelah semua terisi, ubah jadi NOT NULL
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->string('no_registrasi')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropUnique(['no_registrasi']);
            $table->dropColumn('no_registrasi');
        });
    }
};
