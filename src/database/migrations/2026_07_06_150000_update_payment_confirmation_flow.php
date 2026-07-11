<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggans', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('pembayarans', function (Blueprint $table): void {
            $table->string('bukti_pembayaran')->nullable()->after('tanggal_pembayaran');
            $table->index('bukti_pembayaran');
        });

        Schema::table('pengaturan_sistems', function (Blueprint $table): void {
            $table->string('qris_image')->nullable()->after('logo');
        });

        DB::statement("UPDATE pembayarans SET metode_pembayaran = 'qris' WHERE metode_pembayaran <> 'qris'");
        DB::statement("ALTER TABLE pembayarans MODIFY metode_pembayaran ENUM('qris') DEFAULT 'qris'");
        DB::statement("ALTER TABLE pembayarans MODIFY status_pembayaran ENUM('belum_dibayar', 'menunggu_konfirmasi', 'lunas') DEFAULT 'belum_dibayar'");
        DB::statement("ALTER TABLE pesanans MODIFY status_pembayaran ENUM('belum_dibayar', 'menunggu_konfirmasi', 'lunas') DEFAULT 'belum_dibayar'");
    }

    public function down(): void
    {
        DB::statement("UPDATE pembayarans SET status_pembayaran = 'belum_dibayar' WHERE status_pembayaran = 'menunggu_konfirmasi'");
        DB::statement("UPDATE pesanans SET status_pembayaran = 'belum_dibayar' WHERE status_pembayaran = 'menunggu_konfirmasi'");
        DB::statement("ALTER TABLE pembayarans MODIFY status_pembayaran ENUM('belum_dibayar', 'lunas') DEFAULT 'belum_dibayar'");
        DB::statement("ALTER TABLE pesanans MODIFY status_pembayaran ENUM('belum_dibayar', 'lunas') DEFAULT 'belum_dibayar'");
        DB::statement("ALTER TABLE pembayarans MODIFY metode_pembayaran ENUM('tunai', 'transfer_bank', 'qris') DEFAULT 'tunai'");

        Schema::table('pengaturan_sistems', function (Blueprint $table): void {
            $table->dropColumn('qris_image');
        });

        Schema::table('pembayarans', function (Blueprint $table): void {
            $table->dropIndex(['bukti_pembayaran']);
            $table->dropColumn('bukti_pembayaran');
        });

        Schema::table('pelanggans', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
