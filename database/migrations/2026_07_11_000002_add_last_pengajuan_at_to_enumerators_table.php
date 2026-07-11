<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enumerators', function (Blueprint $table) {
            // Untuk tracking cooldown pengajuan tarik saldo (1x per 7 hari)
            $table->timestamp('last_pengajuan_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('enumerators', function (Blueprint $table) {
            $table->dropColumn('last_pengajuan_at');
        });
    }
};
