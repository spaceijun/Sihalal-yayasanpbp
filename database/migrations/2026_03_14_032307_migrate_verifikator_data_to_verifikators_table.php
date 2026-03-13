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
        // 1. Ambil semua nilai unik dari kolom verifikator lama
        $verifikators = DB::table('data_lapangans')
            ->select('verifikator')
            ->whereNotNull('verifikator')
            ->where('verifikator', '!=', '')
            ->distinct()
            ->pluck('verifikator');

        // 2. Insert ke tabel verifikators & update foreign key
        foreach ($verifikators as $nama) {
            $id = DB::table('verifikators')->insertGetId([
                'nama_lengkap'       => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('data_lapangans')
                ->where('verifikator', $nama)
                ->update(['verifikator_id' => $id]);
        }
    }

    public function down(): void
    {
        // Rollback: kembalikan nama dari relasi
        DB::table('data_lapangans')->update([
            'verifikator' => DB::raw('(SELECT nama FROM verifikators WHERE verifikators.id = data_lapangans.verifikator_id)')
        ]);
    }
};
