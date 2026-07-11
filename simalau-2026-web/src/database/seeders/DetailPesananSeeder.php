<?php

namespace Database\Seeders;

use App\Models\DetailPesanan;
use App\Models\LayananLaundry;
use App\Models\Pesanan;
use Illuminate\Database\Seeder;

class DetailPesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pesanan1 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0001')->first();
        $pesanan2 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0002')->first();
        $pesanan3 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0003')->first();

        $cuciSetrikaReguler = LayananLaundry::where('slug', 'cuci-setrika-reguler')->first();
        $kemeja = LayananLaundry::where('slug', 'kemeja')->first();
        $cuciSepatuReguler = LayananLaundry::where('slug', 'cuci-sepatu-reguler')->first();

        $detailPesanans = [
            [
                'pesanan_id' => $pesanan1?->id,
                'layanan_laundry_id' => $cuciSetrikaReguler?->id,
                'nama_layanan' => 'Cuci Setrika Reguler',
                'tipe_layanan' => 'kiloan',
                'berat' => 3,
                'jumlah_item' => null,
                'satuan_hitung' => 'kg',
                'harga_satuan' => 8000,
                'subtotal' => 24000,
                'catatan' => 'Cucian reguler 3 kg.',
            ],
            [
                'pesanan_id' => $pesanan2?->id,
                'layanan_laundry_id' => $kemeja?->id,
                'nama_layanan' => 'Kemeja',
                'tipe_layanan' => 'satuan',
                'berat' => null,
                'jumlah_item' => 3,
                'satuan_hitung' => 'pcs',
                'harga_satuan' => 7000,
                'subtotal' => 21000,
                'catatan' => 'Kemeja dengan noda kopi.',
            ],
            [
                'pesanan_id' => $pesanan2?->id,
                'layanan_laundry_id' => $cuciSetrikaReguler?->id,
                'nama_layanan' => 'Cuci Setrika Reguler',
                'tipe_layanan' => 'kiloan',
                'berat' => 1.125,
                'jumlah_item' => null,
                'satuan_hitung' => 'kg',
                'harga_satuan' => 8000,
                'subtotal' => 9000,
                'catatan' => 'Tambahan cucian kiloan.',
            ],
            [
                'pesanan_id' => $pesanan3?->id,
                'layanan_laundry_id' => $cuciSepatuReguler?->id,
                'nama_layanan' => 'Cuci Sepatu Reguler',
                'tipe_layanan' => 'satuan',
                'berat' => null,
                'jumlah_item' => 1,
                'satuan_hitung' => 'pasang',
                'harga_satuan' => 25000,
                'subtotal' => 25000,
                'catatan' => 'Sepatu putih satu pasang.',
            ],
        ];

        foreach ($detailPesanans as $detail) {
            if (! $detail['pesanan_id'] || ! $detail['layanan_laundry_id']) {
                continue;
            }

            DetailPesanan::updateOrCreate(
                [
                    'pesanan_id' => $detail['pesanan_id'],
                    'layanan_laundry_id' => $detail['layanan_laundry_id'],
                    'nama_layanan' => $detail['nama_layanan'],
                ],
                [
                    'tipe_layanan' => $detail['tipe_layanan'],
                    'berat' => $detail['berat'],
                    'jumlah_item' => $detail['jumlah_item'],
                    'satuan_hitung' => $detail['satuan_hitung'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $detail['subtotal'],
                    'catatan' => $detail['catatan'],
                ]
            );
        }
    }
}