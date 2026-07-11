<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('layanan_laundries', function (Blueprint $table) {
            if (! Schema::hasColumn('layanan_laundries', 'gambar')) {
                $table->string('gambar')->nullable()->after('deskripsi');
            }
        });

        Schema::table('detail_pesanans', function (Blueprint $table) {
            if (! Schema::hasColumn('detail_pesanans', 'gambar_item')) {
                $table->string('gambar_item')->nullable()->after('catatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('detail_pesanans', function (Blueprint $table) {
            if (Schema::hasColumn('detail_pesanans', 'gambar_item')) {
                $table->dropColumn('gambar_item');
            }
        });

        Schema::table('layanan_laundries', function (Blueprint $table) {
            if (Schema::hasColumn('layanan_laundries', 'gambar')) {
                $table->dropColumn('gambar');
            }
        });
    }
};
