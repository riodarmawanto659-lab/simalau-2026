<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pengingat_pengambilans') && Schema::hasColumn('pengingat_pengambilans', 'tanggal_siap_diambil')) {
            DB::statement('ALTER TABLE pengingat_pengambilans MODIFY tanggal_siap_diambil DATETIME NULL');
        }
    }

    public function down(): void
    {
        // Sengaja tidak dikembalikan ke NOT NULL karena data pengingat bisa dibuat bertahap.
    }
};
