<?php

namespace Database\Seeders;

use App\Models\ArusKas;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ArusKasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first()
            ?? User::first();

        $pesanan1 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0001')->first();
        $pembayaran1 = Pembayaran::where('nomor_pembayaran', 'PAY-20260705-0001')->first();

        $arusKas = [
            [
                'pesanan_id' => $pesanan1?->id,
                'pembayaran_id' => $pembayaran1?->id,
                'user_id' => $admin?->id,
                'jenis' => 'masuk',
                'kategori' => 'Pembayaran Laundry',
                'judul' => 'Pembayaran Pesanan LDR-20260705-0001',
                'nominal' => 24000,
                'tanggal' => Carbon::now()->subDays(5)->toDateString(),
                'keterangan' => 'Kas masuk dari pembayaran laundry pelanggan.',
            ],
            [
                'pesanan_id' => null,
                'pembayaran_id' => null,
                'user_id' => $admin?->id,
                'jenis' => 'keluar',
                'kategori' => 'Pembelian Deterjen',
                'judul' => 'Pembelian Deterjen Cair',
                'nominal' => 75000,
                'tanggal' => Carbon::now()->subDays(4)->toDateString(),
                'keterangan' => 'Pembelian deterjen untuk kebutuhan operasional laundry.',
            ],
            [
                'pesanan_id' => null,
                'pembayaran_id' => null,
                'user_id' => $admin?->id,
                'jenis' => 'keluar',
                'kategori' => 'Biaya Listrik',
                'judul' => 'Pembayaran Listrik Operasional',
                'nominal' => 150000,
                'tanggal' => Carbon::now()->subDays(3)->toDateString(),
                'keterangan' => 'Biaya listrik untuk mesin cuci, pengering, dan setrika.',
            ],
            [
                'pesanan_id' => null,
                'pembayaran_id' => null,
                'user_id' => $admin?->id,
                'jenis' => 'keluar',
                'kategori' => 'Perlengkapan Laundry',
                'judul' => 'Pembelian Plastik Kemasan',
                'nominal' => 50000,
                'tanggal' => Carbon::now()->subDays(2)->toDateString(),
                'keterangan' => 'Pembelian plastik kemasan cucian pelanggan.',
            ],
        ];

        foreach ($arusKas as $kas) {
            ArusKas::updateOrCreate(
                [
                    'jenis' => $kas['jenis'],
                    'judul' => $kas['judul'],
                    'tanggal' => $kas['tanggal'],
                ],
                [
                    'pesanan_id' => $kas['pesanan_id'],
                    'pembayaran_id' => $kas['pembayaran_id'],
                    'user_id' => $kas['user_id'],
                    'kategori' => $kas['kategori'],
                    'nominal' => $kas['nominal'],
                    'keterangan' => $kas['keterangan'],
                ]
            );
        }
    }
}