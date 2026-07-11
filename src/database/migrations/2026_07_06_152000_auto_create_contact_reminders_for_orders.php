<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengingat_pengambilans', function (Blueprint $table): void {
            $table->dateTime('tanggal_siap_diambil')->nullable()->change();
        });

        $now = now();
        $pesanans = DB::table('pesanans')
            ->leftJoin('pengingat_pengambilans', 'pengingat_pengambilans.pesanan_id', '=', 'pesanans.id')
            ->whereNull('pengingat_pengambilans.id')
            ->select('pesanans.id', 'pesanans.pelanggan_id', 'pesanans.tanggal_masuk', 'pesanans.tanggal_siap_diambil', 'pesanans.status_pesanan')
            ->orderBy('pesanans.id')
            ->get();

        foreach ($pesanans as $pesanan) {
            $tanggalSiapDiambil = $pesanan->tanggal_siap_diambil
                ? Carbon::parse($pesanan->tanggal_siap_diambil)
                : null;

            DB::table('pengingat_pengambilans')->insert([
                'pesanan_id' => $pesanan->id,
                'pelanggan_id' => $pesanan->pelanggan_id,
                'tanggal_siap_diambil' => $pesanan->tanggal_siap_diambil,
                'tanggal_masuk_pengingat' => $pesanan->tanggal_masuk ?: $now,
                'jumlah_hari_tertahan' => $tanggalSiapDiambil && $tanggalSiapDiambil->isPast()
                    ? (int) max($tanggalSiapDiambil->diffInDays($now), 0)
                    : 0,
                'status_pengingat' => $pesanan->status_pesanan === 'selesai' ? 'selesai' : 'aktif',
                'tanggal_dihubungi' => null,
                'catatan' => 'Pengingat kontak otomatis dibuat dari pesanan pelanggan.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('pengingat_pengambilans')
            ->whereNull('tanggal_siap_diambil')
            ->update(['tanggal_siap_diambil' => DB::raw('tanggal_masuk_pengingat')]);

        Schema::table('pengingat_pengambilans', function (Blueprint $table): void {
            $table->dateTime('tanggal_siap_diambil')->nullable(false)->change();
        });
    }
};
