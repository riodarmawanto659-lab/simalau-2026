<?php

namespace Database\Seeders;

use App\Models\KategoriLayanan;
use App\Models\LayananLaundry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LayananLaundrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriKiloan = KategoriLayanan::where('slug', 'laundry-kiloan')->first();
        $kategoriSatuan = KategoriLayanan::where('slug', 'laundry-satuan')->first();
        $kategoriSepatu = KategoriLayanan::where('slug', 'cuci-sepatu')->first();
        $kategoriSelimut = KategoriLayanan::where('slug', 'cuci-selimut')->first();
        $kategoriKarpet = KategoriLayanan::where('slug', 'cuci-karpet')->first();

        $layananLaundries = [
            [
                'kategori_layanan_id' => $kategoriKiloan?->id,
                'nama_layanan' => 'Cuci Setrika Reguler',
                'deskripsi' => 'Layanan cuci dan setrika pakaian reguler berdasarkan berat cucian.',
                'tipe_layanan' => 'kiloan',
                'tarif' => 8000,
                'estimasi_hari' => 2,
                'minimal_order' => 3,
                'satuan_hitung' => 'kg',
                'status' => 'aktif',
            ],
            [
                'kategori_layanan_id' => $kategoriKiloan?->id,
                'nama_layanan' => 'Cuci Setrika Express',
                'deskripsi' => 'Layanan cuci dan setrika cepat untuk pelanggan yang membutuhkan proses lebih singkat.',
                'tipe_layanan' => 'kiloan',
                'tarif' => 12000,
                'estimasi_hari' => 1,
                'minimal_order' => 3,
                'satuan_hitung' => 'kg',
                'status' => 'aktif',
            ],
            [
                'kategori_layanan_id' => $kategoriSatuan?->id,
                'nama_layanan' => 'Kemeja',
                'deskripsi' => 'Layanan laundry satuan untuk kemeja.',
                'tipe_layanan' => 'satuan',
                'tarif' => 7000,
                'estimasi_hari' => 2,
                'minimal_order' => 1,
                'satuan_hitung' => 'pcs',
                'status' => 'aktif',
            ],
            [
                'kategori_layanan_id' => $kategoriSatuan?->id,
                'nama_layanan' => 'Jaket',
                'deskripsi' => 'Layanan laundry satuan untuk jaket.',
                'tipe_layanan' => 'satuan',
                'tarif' => 15000,
                'estimasi_hari' => 3,
                'minimal_order' => 1,
                'satuan_hitung' => 'pcs',
                'status' => 'aktif',
            ],
            [
                'kategori_layanan_id' => $kategoriSepatu?->id,
                'nama_layanan' => 'Cuci Sepatu Reguler',
                'deskripsi' => 'Layanan pencucian sepatu standar.',
                'tipe_layanan' => 'satuan',
                'tarif' => 25000,
                'estimasi_hari' => 3,
                'minimal_order' => 1,
                'satuan_hitung' => 'pasang',
                'status' => 'aktif',
            ],
            [
                'kategori_layanan_id' => $kategoriSelimut?->id,
                'nama_layanan' => 'Cuci Selimut',
                'deskripsi' => 'Layanan pencucian selimut berdasarkan jumlah item.',
                'tipe_layanan' => 'satuan',
                'tarif' => 30000,
                'estimasi_hari' => 3,
                'minimal_order' => 1,
                'satuan_hitung' => 'pcs',
                'status' => 'aktif',
            ],
            [
                'kategori_layanan_id' => $kategoriKarpet?->id,
                'nama_layanan' => 'Cuci Karpet',
                'deskripsi' => 'Layanan pencucian karpet berdasarkan luas atau jumlah item.',
                'tipe_layanan' => 'satuan',
                'tarif' => 20000,
                'estimasi_hari' => 4,
                'minimal_order' => 1,
                'satuan_hitung' => 'm2',
                'status' => 'aktif',
            ],
        ];

        foreach ($layananLaundries as $layanan) {
            if (! $layanan['kategori_layanan_id']) {
                continue;
            }

            LayananLaundry::updateOrCreate(
                ['slug' => Str::slug($layanan['nama_layanan'])],
                [
                    'kategori_layanan_id' => $layanan['kategori_layanan_id'],
                    'nama_layanan' => $layanan['nama_layanan'],
                    'deskripsi' => $layanan['deskripsi'],
                    'tipe_layanan' => $layanan['tipe_layanan'],
                    'tarif' => $layanan['tarif'],
                    'estimasi_hari' => $layanan['estimasi_hari'],
                    'minimal_order' => $layanan['minimal_order'],
                    'satuan_hitung' => $layanan['satuan_hitung'],
                    'status' => $layanan['status'],
                ]
            );
        }
    }
}