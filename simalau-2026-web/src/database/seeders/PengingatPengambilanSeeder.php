<?php

namespace Database\Seeders;

use App\Models\PengingatPengambilan;
use App\Models\Pesanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PengingatPengambilanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pesanan = Pesanan::where('nomor_pesanan', 'LDR-20260705-0001')->first();

        if (! $pesanan || ! $pesanan->pelanggan_id || ! $pesanan->tanggal_siap_diambil) {
            return;
        }

        PengingatPengambilan::updateOrCreate(
            ['pesanan_id' => $pesanan->id],
            [
                'pelanggan_id' => $pesanan->pelanggan_id,
                'tanggal_siap_diambil' => $pesanan->tanggal_siap_diambil,
                'tanggal_masuk_pengingat' => Carbon::parse($pesanan->tanggal_siap_diambil)->addDays(3),
                'jumlah_hari_tertahan' => Carbon::parse($pesanan->tanggal_siap_diambil)->diffInDays(now()),
                'status_pengingat' => 'aktif',
                'tanggal_dihubungi' => null,
                'catatan' => 'Pengingat kontak otomatis dari pesanan. Pelanggan dapat dihubungi melalui WhatsApp.',
            ]
        );
    }
}
