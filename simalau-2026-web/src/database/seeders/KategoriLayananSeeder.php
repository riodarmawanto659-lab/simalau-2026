<?php

namespace Database\Seeders;

use App\Models\KategoriLayanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriLayanans = [
            [
                'nama_kategori' => 'Laundry Kiloan',
                'deskripsi' => 'Kategori layanan laundry berdasarkan berat cucian per kilogram.',
                'status' => 'aktif',
                'urutan' => 1,
            ],
            [
                'nama_kategori' => 'Laundry Satuan',
                'deskripsi' => 'Kategori layanan laundry berdasarkan jumlah item cucian.',
                'status' => 'aktif',
                'urutan' => 2,
            ],
            [
                'nama_kategori' => 'Cuci Sepatu',
                'deskripsi' => 'Kategori layanan pencucian sepatu.',
                'status' => 'aktif',
                'urutan' => 3,
            ],
            [
                'nama_kategori' => 'Cuci Selimut',
                'deskripsi' => 'Kategori layanan pencucian selimut.',
                'status' => 'aktif',
                'urutan' => 4,
            ],
            [
                'nama_kategori' => 'Cuci Karpet',
                'deskripsi' => 'Kategori layanan pencucian karpet.',
                'status' => 'aktif',
                'urutan' => 5,
            ],
        ];

        foreach ($kategoriLayanans as $kategori) {
            KategoriLayanan::updateOrCreate(
                ['slug' => Str::slug($kategori['nama_kategori'])],
                [
                    'nama_kategori' => $kategori['nama_kategori'],
                    'deskripsi' => $kategori['deskripsi'],
                    'status' => $kategori['status'],
                    'urutan' => $kategori['urutan'],
                ]
            );
        }
    }
}