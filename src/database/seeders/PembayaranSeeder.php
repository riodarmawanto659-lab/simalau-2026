<?php

namespace Database\Seeders;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pesanan1 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0001')->first();
        $pesanan2 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0002')->first();
        $pesanan3 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0003')->first();

        $pembayarans = [
            [
                'pesanan_id' => $pesanan1?->id,
                'nomor_pembayaran' => 'PAY-20260705-0001',
                'metode_pembayaran' => 'tunai',
                'total_tagihan' => 24000,
                'nominal_dibayar' => 25000,
                'kembalian' => 1000,
                'status_pembayaran' => 'lunas',
                'tanggal_pembayaran' => Carbon::now()->subDays(5),
                'catatan' => 'Pembayaran tunai lunas.',
            ],
            [
                'pesanan_id' => $pesanan2?->id,
                'nomor_pembayaran' => 'PAY-20260705-0002',
                'metode_pembayaran' => 'qris',
                'total_tagihan' => 30000,
                'nominal_dibayar' => 0,
                'kembalian' => 0,
                'status_pembayaran' => 'belum_dibayar',
                'tanggal_pembayaran' => null,
                'catatan' => 'Menunggu pembayaran dari pelanggan.',
            ],
            [
                'pesanan_id' => $pesanan3?->id,
                'nomor_pembayaran' => 'PAY-20260705-0003',
                'metode_pembayaran' => 'transfer_bank',
                'total_tagihan' => 25000,
                'nominal_dibayar' => 0,
                'kembalian' => 0,
                'status_pembayaran' => 'belum_dibayar',
                'tanggal_pembayaran' => null,
                'catatan' => 'Pembayaran belum dilakukan.',
            ],
        ];

        foreach ($pembayarans as $pembayaran) {
            if (! $pembayaran['pesanan_id']) {
                continue;
            }

            Pembayaran::updateOrCreate(
                ['nomor_pembayaran' => $pembayaran['nomor_pembayaran']],
                [
                    'pesanan_id' => $pembayaran['pesanan_id'],
                    'metode_pembayaran' => $pembayaran['metode_pembayaran'],
                    'total_tagihan' => $pembayaran['total_tagihan'],
                    'nominal_dibayar' => $pembayaran['nominal_dibayar'],
                    'kembalian' => $pembayaran['kembalian'],
                    'status_pembayaran' => $pembayaran['status_pembayaran'],
                    'tanggal_pembayaran' => $pembayaran['tanggal_pembayaran'],
                    'catatan' => $pembayaran['catatan'],
                ]
            );
        }
    }
}