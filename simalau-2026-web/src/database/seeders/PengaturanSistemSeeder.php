<?php

namespace Database\Seeders;

use App\Models\PengaturanSistem;
use Illuminate\Database\Seeder;

class PengaturanSistemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PengaturanSistem::updateOrCreate(
            ['nama_laundry' => 'LaundryKita'],
            [
                'alamat' => 'Jl. sabilillah rt 004 rw 003, tangerang',
                'nomor_whatsapp' => '0881012056484',
                'email' => 'idoyrio37@gmail.com',
                'jam_buka' => '08:00:00',
                'jam_tutup' => '20:00:00',
                'deskripsi' => 'LaundryKita adalah layanan laundry berbasis web yang melayani laundry kiloan, laundry satuan, cuci sepatu, cuci selimut, dan cuci karpet.',
                'catatan_nota' => 'Terima kasih telah menggunakan layanan LaundryKita. Simpan nota ini sebagai bukti transaksi.',
                'logo' => null,
                'latitude' => null,
                'longitude' => null,
                'status_sistem' => 'aktif',
            ]
        );
    }
}