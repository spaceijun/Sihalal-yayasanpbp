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
        Schema::table('koordinators', function (Blueprint $table) {
            $table->decimal('fee_enum', 15, 2)->after('telephone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('koordinators', function (Blueprint $table) {
            $table->dropColumn('fee_enum');
        });
    }
};
