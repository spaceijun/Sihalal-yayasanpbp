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
            $table->enum('recruit_type', ['PENDAMPING', 'DATA ENTRY', 'ADMIN UMUM'])->after('koordinator_id');
            $table->enum('type_entry', ['OSS', 'SIHALAL'])->nullable()->after('recruit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruitments', function (Blueprint $table) {
            $table->dropColumn('recruit_type');
            $table->dropColumn('type_entry');
        });
    }
};
