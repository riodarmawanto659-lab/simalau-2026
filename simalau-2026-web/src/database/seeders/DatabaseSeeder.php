<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,

            KategoriLayananSeeder::class,
            LayananLaundrySeeder::class,
            PelangganSeeder::class,
            PesananSeeder::class,
            DetailPesananSeeder::class,
            PembayaranSeeder::class,
            RiwayatStatusSeeder::class,
            PengingatPengambilanSeeder::class,
            ArusKasSeeder::class,
            PengaturanSistemSeeder::class,
            HariLiburSeeder::class,
        ]);
    }
}