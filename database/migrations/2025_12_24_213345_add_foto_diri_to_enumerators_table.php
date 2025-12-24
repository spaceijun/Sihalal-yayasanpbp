<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Symfony\Component\Translation\t;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('enumerators', function (Blueprint $table) {
            $table->string('foto_diri')->nullable()->after('telephone');
            $table->string('no_registrasi')->unique()->after('foto_diri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enumerators', function (Blueprint $table) {
            $table->dropColumn('foto_diri');
            $table->dropColumn('no_registrasi');
        });
    }
};
