<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settingwebsites', function (Blueprint $table) {
            $table->string('urusin_base_url')->nullable()->after('anthropic_api_key');
            $table->text('urusin_api_key')->nullable()->after('urusin_base_url');
        });
    }

    public function down(): void
    {
        Schema::table('settingwebsites', function (Blueprint $table) {
            $table->dropColumn(['urusin_base_url', 'urusin_api_key']);
        });
    }
};
