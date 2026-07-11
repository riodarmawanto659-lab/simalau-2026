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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pelanggan_id')
                ->constrained('pelanggans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nomor_pesanan')->unique();

            $table->dateTime('tanggal_masuk')->nullable();
            $table->dateTime('estimasi_selesai')->nullable();
            $table->dateTime('tanggal_siap_diambil')->nullable();
            $table->dateTime('tanggal_selesai')->nullable();

            $table->enum('metode_penyerahan', ['antar_sendiri', 'jemput'])
                ->default('antar_sendiri');

            $table->text('alamat_penjemputan')->nullable();
            $table->text('catatan_pelanggan')->nullable();
            $table->text('catatan_admin')->nullable();

            $table->enum('status_pesanan', [
                'menunggu_konfirmasi',
                'menunggu_proses',
                'sedang_dicuci',
                'sedang_dikeringkan',
                'sedang_disetrika',
                'siap_diambil',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_konfirmasi');

            $table->enum('status_pembayaran', ['belum_dibayar', 'lunas'])
                ->default('belum_dibayar');

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total_biaya', 12, 2)->default(0);

            $table->unsignedInteger('urutan_antrian')->nullable();

            $table->timestamps();

            $table->index('pelanggan_id');
            $table->index('nomor_pesanan');
            $table->index('tanggal_masuk');
            $table->index('estimasi_selesai');
            $table->index('status_pesanan');
            $table->index('status_pembayaran');
            $table->index('urutan_antrian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};