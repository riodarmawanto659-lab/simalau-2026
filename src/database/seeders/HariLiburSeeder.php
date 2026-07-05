<?php

namespace Database\Seeders;

use App\Models\HariLibur;
use Illuminate\Database\Seeder;

class HariLiburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hariLiburs = [
            [
                'nama_hari_libur' => 'Tahun Baru',
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => null,
                'jenis' => 'nasional',
                'keterangan' => 'Libur nasional Tahun Baru.',
                'status' => 'aktif',
            ],
            [
                'nama_hari_libur' => 'Libur Idul Fitri',
                'tanggal_mulai' => '2026-03-20',
                'tanggal_selesai' => '2026-03-22',
                'jenis' => 'nasional',
                'keterangan' => 'Libur operasional selama periode Idul Fitri.',
                'status' => 'aktif',
            ],
            [
                'nama_hari_libur' => 'Hari Kemerdekaan',
                'tanggal_mulai' => '2026-08-17',
                'tanggal_selesai' => null,
                'jenis' => 'nasional',
                'keterangan' => 'Libur nasional Hari Kemerdekaan Republik Indonesia.',
                'status' => 'aktif',
            ],
            [
                'nama_hari_libur' => 'Maintenance Mesin Laundry',
                'tanggal_mulai' => '2026-07-10',
                'tanggal_selesai' => null,
                'jenis' => 'operasional',
                'keterangan' => 'Libur sementara karena perawatan mesin laundry.',
                'status' => 'aktif',
            ],
        ];

        foreach ($hariLiburs as $hariLibur) {
            HariLibur::updateOrCreate(
                [
                    'nama_hari_libur' => $hariLibur['nama_hari_libur'],
                    'tanggal_mulai' => $hariLibur['tanggal_mulai'],
                ],
                [
                    'tanggal_selesai' => $hariLibur['tanggal_selesai'],
                    'jenis' => $hariLibur['jenis'],
                    'keterangan' => $hariLibur['keterangan'],
                    'status' => $hariLibur['status'],
                ]
            );
        }
    }
}