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
        Schema::create('pengaturan_sistems', function (Blueprint $table) {
            $table->id();

            $table->string('nama_laundry')->default('LaundryKita');
            $table->text('alamat')->nullable();
            $table->string('nomor_whatsapp', 20)->nullable();
            $table->string('email')->nullable();

            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();

            $table->text('deskripsi')->nullable();
            $table->text('catatan_nota')->nullable();

            $table->string('logo')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->enum('status_sistem', [
                'aktif',
                'nonaktif',
            ])->default('aktif');

            $table->timestamps();

            $table->index('status_sistem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sistems');
    }
};