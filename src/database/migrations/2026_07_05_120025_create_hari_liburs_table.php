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
        Schema::create('hari_liburs', function (Blueprint $table) {
            $table->id();

            $table->string('nama_hari_libur');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();

            $table->enum('jenis', [
                'nasional',
                'operasional',
                'lainnya',
            ])->default('operasional');

            $table->text('keterangan')->nullable();

            $table->enum('status', [
                'aktif',
                'nonaktif',
            ])->default('aktif');

            $table->timestamps();

            $table->index('nama_hari_libur');
            $table->index('tanggal_mulai');
            $table->index('tanggal_selesai');
            $table->index('jenis');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_liburs');
    }
};