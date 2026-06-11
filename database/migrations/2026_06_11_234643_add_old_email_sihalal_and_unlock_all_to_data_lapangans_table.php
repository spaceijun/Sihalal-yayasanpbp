<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->string('old_email_sihalal')->nullable()->after('email_sihalal');
        });

        // Buka semua data lapangan agar terlihat di data_entry secara default
        DB::table('data_lapangans')->update(['is_unlocked_for_data_entry' => true]);
    }

    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn('old_email_sihalal');
        });
    }
};
