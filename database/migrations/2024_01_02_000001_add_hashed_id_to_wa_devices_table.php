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
        Schema::table('wa_devices', function (Blueprint $table) {
            $table->string('hashed_id', 100)->nullable()->after('id');
        });

        // Generate hashed_id for existing records
        \App\Models\Superadmin\WaDevice::chunk(100, function ($devices) {
            foreach ($devices as $device) {
                if (empty($device->hashed_id)) {
                    $device->hashed_id = \App\Models\Superadmin\WaDevice::generateHashedId();
                    $device->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_devices', function (Blueprint $table) {
            $table->dropColumn('hashed_id');
        });
    }
};
