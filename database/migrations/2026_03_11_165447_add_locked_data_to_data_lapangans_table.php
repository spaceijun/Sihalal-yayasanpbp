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
            $table->boolean('is_being_edited')->default(false);
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('edit_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropColumn('is_being_edited');
            $table->dropColumn('edited_by');
            $table->dropColumn('edit_expires_at');
        });
    }
};
