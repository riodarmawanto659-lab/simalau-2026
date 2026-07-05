<?php

namespace Database\Seeders;

use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PelangganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pelanggans = [
            [
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'nomor_whatsapp' => '081234567890',
                'alamat' => 'Jl. Melati No. 12, Bandung',
                'password' => 'password',
            ],
            [
                'nama_lengkap' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'nomor_whatsapp' => '081298765432',
                'alamat' => 'Jl. Mawar No. 8, Bandung',
                'password' => 'password',
            ],
            [
                'nama_lengkap' => 'Rizky Pratama',
                'email' => 'rizky@example.com',
                'nomor_whatsapp' => '081377788899',
                'alamat' => 'Jl. Kenanga No. 21, Bandung',
                'password' => 'password',
            ],
        ];

        foreach ($pelanggans as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nama_lengkap'],
                    'password' => Hash::make($data['password']),
                ]
            );

            Pelanggan::updateOrCreate(
                ['email' => $data['email']],
                [
                    'user_id' => $user->id,
                    'nama_lengkap' => $data['nama_lengkap'],
                    'nomor_whatsapp' => $data['nomor_whatsapp'],
                    'alamat' => $data['alamat'],
                    'status' => 'aktif',
                ]
            );
        }
    }
}