<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah default column ke true agar record baru otomatis terlihat di data_entry
        DB::statement('ALTER TABLE data_lapangans ALTER COLUMN is_unlocked_for_data_entry SET DEFAULT 1');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE data_lapangans ALTER COLUMN is_unlocked_for_data_entry SET DEFAULT 0');
    }
};
