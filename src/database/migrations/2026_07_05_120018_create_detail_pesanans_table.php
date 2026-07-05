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
        Schema::create('detail_pesanans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pesanan_id')
                ->constrained('pesanans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('layanan_laundry_id')
                ->constrained('layanan_laundries')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nama_layanan');
            $table->enum('tipe_layanan', ['kiloan', 'satuan']);

            $table->decimal('berat', 8, 2)->nullable();
            $table->unsignedInteger('jumlah_item')->nullable();

            $table->string('satuan_hitung')->default('kg');

            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->index('pesanan_id');
            $table->index('layanan_laundry_id');
            $table->index('tipe_layanan');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanans');
    }
};