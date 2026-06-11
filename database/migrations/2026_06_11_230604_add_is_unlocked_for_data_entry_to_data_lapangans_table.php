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
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->boolean('is_unlocked_for_data_entry')->default(false)->after('edit_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn('is_unlocked_for_data_entry');
        });
    }
};
