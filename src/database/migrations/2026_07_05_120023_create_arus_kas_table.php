<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('arus_kas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pesanan_id')
                ->nullable()
                ->constrained('pesanans')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('pembayaran_id')
                ->nullable()
                ->constrained('pembayarans')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('jenis', [
                'masuk',
                'keluar',
            ]);

            $table->string('kategori');
            $table->string('judul');
            $table->decimal('nominal', 12, 2)->default(0);

            $table->date('tanggal');
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index('pesanan_id');
            $table->index('pembayaran_id');
            $table->index('user_id');
            $table->index('jenis');
            $table->index('kategori');
            $table->index('tanggal');
        });
    }

  
    public function down(): void
    {
        Schema::dropIfExists('arus_kas');
    }
};