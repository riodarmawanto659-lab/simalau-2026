<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $budi = Pelanggan::where('email', 'budi@example.com')->first();
        $siti = Pelanggan::where('email', 'siti@example.com')->first();
        $rizky = Pelanggan::where('email', 'rizky@example.com')->first();

        $pesanans = [
            [
                'pelanggan_id' => $budi?->id,
                'nomor_pesanan' => 'LDR-20260705-0001',
                'tanggal_masuk' => Carbon::now()->subDays(5),
                'estimasi_selesai' => Carbon::now()->subDays(3),
                'tanggal_siap_diambil' => Carbon::now()->subDays(3),
                'tanggal_selesai' => null,
                'metode_penyerahan' => 'antar_sendiri',
                'alamat_penjemputan' => null,
                'catatan_pelanggan' => 'Tolong jangan pakai pewangi terlalu kuat.',
                'catatan_admin' => 'Cucian sudah selesai dan menunggu diambil.',
                'status_pesanan' => 'siap_diambil',
                'status_pembayaran' => 'lunas',
                'subtotal' => 24000,
                'diskon' => 0,
                'total_biaya' => 24000,
                'urutan_antrian' => 1,
            ],
            [
                'pelanggan_id' => $siti?->id,
                'nomor_pesanan' => 'LDR-20260705-0002',
                'tanggal_masuk' => Carbon::now()->subDays(2),
                'estimasi_selesai' => Carbon::now(),
                'tanggal_siap_diambil' => null,
                'tanggal_selesai' => null,
                'metode_penyerahan' => 'jemput',
                'alamat_penjemputan' => 'Jl. Mawar No. 8, Bandung',
                'catatan_pelanggan' => 'Ada noda kopi di kemeja.',
                'catatan_admin' => 'Sedang dalam proses pencucian.',
                'status_pesanan' => 'sedang_dicuci',
                'status_pembayaran' => 'belum_dibayar',
                'subtotal' => 30000,
                'diskon' => 0,
                'total_biaya' => 30000,
                'urutan_antrian' => 2,
            ],
            [
                'pelanggan_id' => $rizky?->id,
                'nomor_pesanan' => 'LDR-20260705-0003',
                'tanggal_masuk' => Carbon::now()->subDay(),
                'estimasi_selesai' => Carbon::now()->addDays(2),
                'tanggal_siap_diambil' => null,
                'tanggal_selesai' => null,
                'metode_penyerahan' => 'antar_sendiri',
                'alamat_penjemputan' => null,
                'catatan_pelanggan' => 'Sepatu warna putih, mohon hati-hati.',
                'catatan_admin' => 'Menunggu proses antrean FIFO.',
                'status_pesanan' => 'menunggu_proses',
                'status_pembayaran' => 'belum_dibayar',
                'subtotal' => 25000,
                'diskon' => 0,
                'total_biaya' => 25000,
                'urutan_antrian' => 3,
            ],
        ];

        foreach ($pesanans as $pesanan) {
            if (! $pesanan['pelanggan_id']) {
                continue;
            }

            Pesanan::updateOrCreate(
                ['nomor_pesanan' => $pesanan['nomor_pesanan']],
                [
                    'pelanggan_id' => $pesanan['pelanggan_id'],
                    'tanggal_masuk' => $pesanan['tanggal_masuk'],
                    'estimasi_selesai' => $pesanan['estimasi_selesai'],
                    'tanggal_siap_diambil' => $pesanan['tanggal_siap_diambil'],
                    'tanggal_selesai' => $pesanan['tanggal_selesai'],
                    'metode_penyerahan' => $pesanan['metode_penyerahan'],
                    'alamat_penjemputan' => $pesanan['alamat_penjemputan'],
                    'catatan_pelanggan' => $pesanan['catatan_pelanggan'],
                    'catatan_admin' => $pesanan['catatan_admin'],
                    'status_pesanan' => $pesanan['status_pesanan'],
                    'status_pembayaran' => $pesanan['status_pembayaran'],
                    'subtotal' => $pesanan['subtotal'],
                    'diskon' => $pesanan['diskon'],
                    'total_biaya' => $pesanan['total_biaya'],
                    'urutan_antrian' => $pesanan['urutan_antrian'],
                ]
            );
        }
    }
}