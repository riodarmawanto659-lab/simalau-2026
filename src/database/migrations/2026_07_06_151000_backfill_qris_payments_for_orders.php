<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = 'PAY-' . now()->format('Ymd') . '-';
        $lastNumber = DB::table('pembayarans')
            ->where('nomor_pembayaran', 'like', $prefix . '%')
            ->orderByDesc('nomor_pembayaran')
            ->value('nomor_pembayaran');

        $sequence = $lastNumber ? ((int) substr((string) $lastNumber, -4)) + 1 : 1;
        $now = now();

        $pesanans = DB::table('pesanans')
            ->leftJoin('pembayarans', 'pembayarans.pesanan_id', '=', 'pesanans.id')
            ->whereNull('pembayarans.id')
            ->select('pesanans.id', 'pesanans.total_biaya', 'pesanans.status_pembayaran')
            ->orderBy('pesanans.id')
            ->get();

        foreach ($pesanans as $pesanan) {
            $statusPembayaran = in_array($pesanan->status_pembayaran, ['menunggu_konfirmasi', 'lunas'], true)
                ? $pesanan->status_pembayaran
                : 'belum_dibayar';

            $totalTagihan = (float) $pesanan->total_biaya;
            $nominalDibayar = $statusPembayaran === 'lunas' ? $totalTagihan : 0;

            DB::table('pembayarans')->insert([
                'pesanan_id' => $pesanan->id,
                'nomor_pembayaran' => $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
                'metode_pembayaran' => 'qris',
                'total_tagihan' => $totalTagihan,
                'nominal_dibayar' => $nominalDibayar,
                'kembalian' => max($nominalDibayar - $totalTagihan, 0),
                'status_pembayaran' => $statusPembayaran,
                'tanggal_pembayaran' => $statusPembayaran === 'lunas' ? $now : null,
                'bukti_pembayaran' => null,
                'catatan' => 'Tagihan QRIS otomatis dibuat untuk pesanan yang sudah ada.',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $sequence++;
        }
    }

    public function down(): void
    {
        //
    }
};
