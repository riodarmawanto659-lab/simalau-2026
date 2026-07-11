<?php

namespace App\Console\Commands;

use App\Models\PengingatPengambilan;
use App\Models\Pesanan;
use Illuminate\Console\Command;

class GeneratePengingatPengambilan extends Command
{
    protected $signature = 'laundry:generate-pengingat-pengambilan';

    protected $description = 'Membuat pengingat untuk cucian siap diambil yang belum diambil minimal 3 hari.';

    public function handle(): int
    {
        $pesanans = Pesanan::query()
            ->where('status_pesanan', 'siap_diambil')
            ->whereNotNull('tanggal_siap_diambil')
            ->where('tanggal_siap_diambil', '<=', now()->subDays(3))
            ->with('pelanggan')
            ->get();

        foreach ($pesanans as $pesanan) {
            PengingatPengambilan::updateOrCreate(
                ['pesanan_id' => $pesanan->id],
                [
                    'pelanggan_id' => $pesanan->pelanggan_id,
                    'tanggal_siap_diambil' => $pesanan->tanggal_siap_diambil,
                    'tanggal_masuk_pengingat' => now(),
                    'jumlah_hari_tertahan' => $pesanan->tanggal_siap_diambil->diffInDays(now()),
                    'status_pengingat' => $pesanan->pengingatPengambilan?->status_pengingat ?? 'aktif',
                    'catatan' => $pesanan->pengingatPengambilan?->catatan ?? 'Otomatis dibuat sistem karena cucian belum diambil minimal 3 hari.',
                ]
            );
        }

        $this->info('Pengingat pengambilan diperbarui: ' . $pesanans->count() . ' pesanan.');

        return self::SUCCESS;
    }
}
