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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('nomor_whatsapp', 20);
            $table->text('alamat')->nullable();

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->timestamps();

            $table->index('nama_lengkap');
            $table->index('nomor_whatsapp');
            $table->index('status');
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};