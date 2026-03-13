<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $verifikators = DB::table('data_lapangans')
            ->select('verifikator')
            ->whereNotNull('verifikator')
            ->where('verifikator', '!=', '')
            ->distinct()
            ->pluck('verifikator');

        foreach ($verifikators as $nama) {
            $id = DB::table('verifikators')->insertGetId([
                'nama_lengkap'   => $nama,
                'telephone'      => '-',   // placeholder, isi manual nanti
                'alamat_lengkap' => '-',   // placeholder, isi manual nanti
                'rate_per_data'  => 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::table('data_lapangans')
                ->where('verifikator', $nama)
                ->update(['verifikator_id' => $id]);
        }
    }

    public function down(): void
    {
        DB::table('data_lapangans')->update([
            'verifikator' => DB::raw(
                '(SELECT nama_lengkap FROM verifikators WHERE verifikators.id = data_lapangans.verifikator_id)'
            )
        ]);
    }
};
