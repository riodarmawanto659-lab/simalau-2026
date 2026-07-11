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
        Schema::create('kategori_layanans', function (Blueprint $table) {
            $table->id();

            $table->string('nama_kategori');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();

            $table->enum('status', ['aktif', 'nonaktif'])
                ->default('aktif');

            $table->integer('urutan')
                ->default(0);

            $table->timestamps();

            $table->index('nama_kategori');
            $table->index('status');
            $table->index('urutan');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('kategori_layanans');
    }
};