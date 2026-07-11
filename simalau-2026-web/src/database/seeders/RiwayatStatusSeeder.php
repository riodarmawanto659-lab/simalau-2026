<?php

namespace Database\Seeders;

use App\Models\Pesanan;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RiwayatStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first()
            ?? User::first();

        $pesanan1 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0001')->first();
        $pesanan2 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0002')->first();
        $pesanan3 = Pesanan::where('nomor_pesanan', 'LDR-20260705-0003')->first();

        $riwayatStatuses = [
            [
                'pesanan_id' => $pesanan1?->id,
                'user_id' => $admin?->id,
                'status_sebelumnya' => null,
                'status_baru' => 'menunggu_proses',
                'tanggal_perubahan' => Carbon::now()->subDays(5),
                'catatan' => 'Pesanan resmi masuk ke antrean laundry.',
            ],
            [
                'pesanan_id' => $pesanan1?->id,
                'user_id' => $admin?->id,
                'status_sebelumnya' => 'menunggu_proses',
                'status_baru' => 'sedang_dicuci',
                'tanggal_perubahan' => Carbon::now()->subDays(5)->addHours(2),
                'catatan' => 'Cucian mulai diproses.',
            ],
            [
                'pesanan_id' => $pesanan1?->id,
                'user_id' => $admin?->id,
                'status_sebelumnya' => 'sedang_dicuci',
                'status_baru' => 'sedang_dikeringkan',
                'tanggal_perubahan' => Carbon::now()->subDays(4),
                'catatan' => 'Cucian masuk tahap pengeringan.',
            ],
            [
                'pesanan_id' => $pesanan1?->id,
                'user_id' => $admin?->id,
                'status_sebelumnya' => 'sedang_dikeringkan',
                'status_baru' => 'sedang_disetrika',
                'tanggal_perubahan' => Carbon::now()->subDays(4)->addHours(3),
                'catatan' => 'Cucian masuk tahap setrika.',
            ],
            [
                'pesanan_id' => $pesanan1?->id,
                'user_id' => $admin?->id,
                'status_sebelumnya' => 'sedang_disetrika',
                'status_baru' => 'siap_diambil',
                'tanggal_perubahan' => Carbon::now()->subDays(3),
                'catatan' => 'Cucian sudah siap diambil.',
            ],

            [
                'pesanan_id' => $pesanan2?->id,
                'user_id' => $admin?->id,
                'status_sebelumnya' => null,
                'status_baru' => 'menunggu_proses',
                'tanggal_perubahan' => Carbon::now()->subDays(2),
                'catatan' => 'Pesanan masuk antrean FIFO.',
            ],
            [
                'pesanan_id' => $pesanan2?->id,
                'user_id' => $admin?->id,
                'status_sebelumnya' => 'menunggu_proses',
                'status_baru' => 'sedang_dicuci',
                'tanggal_perubahan' => Carbon::now()->subDays(2)->addHours(3),
                'catatan' => 'Cucian sedang dicuci.',
            ],

            [
                'pesanan_id' => $pesanan3?->id,
                'user_id' => $admin?->id,
                'status_sebelumnya' => null,
                'status_baru' => 'menunggu_proses',
                'tanggal_perubahan' => Carbon::now()->subDay(),
                'catatan' => 'Pesanan menunggu proses sesuai antrean.',
            ],
        ];

        foreach ($riwayatStatuses as $riwayat) {
            if (! $riwayat['pesanan_id']) {
                continue;
            }

            RiwayatStatus::updateOrCreate(
                [
                    'pesanan_id' => $riwayat['pesanan_id'],
                    'status_baru' => $riwayat['status_baru'],
                    'tanggal_perubahan' => $riwayat['tanggal_perubahan'],
                ],
                [
                    'user_id' => $riwayat['user_id'],
                    'status_sebelumnya' => $riwayat['status_sebelumnya'],
                    'catatan' => $riwayat['catatan'],
                ]
            );
        }
    }
}