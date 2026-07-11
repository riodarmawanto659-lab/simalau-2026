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
        Schema::create('layanan_laundries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_layanan_id')
                ->constrained('kategori_layanans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nama_layanan');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();

            $table->enum('tipe_layanan', ['kiloan', 'satuan']);
            $table->decimal('tarif', 12, 2)->default(0);

            $table->integer('estimasi_hari')->default(1);
            $table->integer('minimal_order')->nullable();

            $table->string('satuan_hitung')->default('kg');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

            $table->timestamps();

            $table->index('kategori_layanan_id');
            $table->index('nama_layanan');
            $table->index('tipe_layanan');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan_laundries');
    }
};