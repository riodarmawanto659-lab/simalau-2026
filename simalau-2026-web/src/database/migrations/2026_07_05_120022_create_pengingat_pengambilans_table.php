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
        Schema::create('pengingat_pengambilans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pesanan_id')
                ->unique()
                ->constrained('pesanans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('pelanggan_id')
                ->constrained('pelanggans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->dateTime('tanggal_siap_diambil')->nullable();
            $table->dateTime('tanggal_masuk_pengingat');

            $table->unsignedInteger('jumlah_hari_tertahan')->default(3);

            $table->enum('status_pengingat', [
                'aktif',
                'sudah_dihubungi',
                'selesai',
            ])->default('aktif');

            $table->dateTime('tanggal_dihubungi')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('pelanggan_id');
            $table->index('tanggal_siap_diambil');
            $table->index('tanggal_masuk_pengingat');
            $table->index('status_pengingat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengingat_pengambilans');
    }
};
