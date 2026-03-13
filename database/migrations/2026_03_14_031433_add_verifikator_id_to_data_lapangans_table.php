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
            $table->foreignId('verifikator_id')->nullable()->after('status')->constrained('verifikators');
            $table->foreignId('payment_id')->nullable()->constrained('verifikator_payments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_lapangans', function (Blueprint $table) {
            $table->dropForeign(['verifikator_id']);
            $table->dropForeign(['payment_id']);
        });
    }
};
