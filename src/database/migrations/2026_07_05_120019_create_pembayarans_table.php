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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pesanan_id')
                ->unique()
                ->constrained('pesanans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('nomor_pembayaran')->unique();

            $table->enum('metode_pembayaran', [
                'tunai',
                'transfer_bank',
                'qris',
            ])->default('tunai');

            $table->decimal('total_tagihan', 12, 2)->default(0);
            $table->decimal('nominal_dibayar', 12, 2)->default(0);
            $table->decimal('kembalian', 12, 2)->default(0);

            $table->enum('status_pembayaran', [
                'belum_dibayar',
                'lunas',
            ])->default('belum_dibayar');

            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('nomor_pembayaran');
            $table->index('metode_pembayaran');
            $table->index('status_pembayaran');
            $table->index('tanggal_pembayaran');
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};