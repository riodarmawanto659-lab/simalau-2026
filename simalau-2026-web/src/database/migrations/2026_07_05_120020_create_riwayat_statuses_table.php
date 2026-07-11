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
        Schema::create('riwayat_statuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pesanan_id')
                ->constrained('pesanans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status_sebelumnya', [
                'menunggu_konfirmasi',
                'menunggu_proses',
                'sedang_dicuci',
                'sedang_dikeringkan',
                'sedang_disetrika',
                'siap_diambil',
                'selesai',
                'dibatalkan',
            ])->nullable();

            $table->enum('status_baru', [
                'menunggu_konfirmasi',
                'menunggu_proses',
                'sedang_dicuci',
                'sedang_dikeringkan',
                'sedang_disetrika',
                'siap_diambil',
                'selesai',
                'dibatalkan',
            ]);

            $table->dateTime('tanggal_perubahan');
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('pesanan_id');
            $table->index('user_id');
            $table->index('status_baru');
            $table->index('tanggal_perubahan');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('riwayat_statuses');
    }
};