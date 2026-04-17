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
            $table->unsignedBigInteger('last_read_pengumuman_id')->nullable()->after('entry_type');
            $table->foreign('last_read_pengumuman_id')
                ->references('id')->on('pengumumans')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_entrys', function (Blueprint $table) {
            $table->dropForeign(['last_read_pengumuman_id']);
            $table->dropColumn('last_read_pengumuman_id');
        });
    }
};
