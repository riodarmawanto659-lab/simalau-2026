<?php

namespace App\Services;

use App\Models\DetailPesanan;
use App\Models\LayananLaundry;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LaundryOrderService
{
    public function createCustomerOrder(User $user, array $data): Pesanan
    {
        return DB::transaction(function () use ($user, $data): Pesanan {
            $pelanggan = $this->resolvePelanggan($user);

            $layanan = LayananLaundry::query()
                ->where('status', 'aktif')
                ->findOrFail($data['layanan_laundry_id']);

            $quantity = $this->normalizeQuantity($layanan, $data);
            $subtotal = $quantity * (float) $layanan->tarif;

            $pesanan = Pesanan::create([
                'pelanggan_id' => $pelanggan->id,
                'nomor_pesanan' => Pesanan::generateNomorPesanan(),
                'tanggal_masuk' => now(),
                'estimasi_selesai' => now()->addDays((int) $layanan->estimasi_hari),
                'metode_penyerahan' => $data['metode_penyerahan'],
                'alamat_penjemputan' => $data['metode_penyerahan'] === 'jemput'
                    ? ($data['alamat_penjemputan'] ?? null)
                    : null,
                'catatan_pelanggan' => $data['catatan_pelanggan'] ?? null,
                'status_pesanan' => 'menunggu_konfirmasi',
                'status_pembayaran' => 'belum_dibayar',
                'subtotal' => $subtotal,
                'diskon' => 0,
                'total_biaya' => $subtotal,
            ]);

            DetailPesanan::create([
                'pesanan_id' => $pesanan->id,
                'layanan_laundry_id' => $layanan->id,
                'nama_layanan' => $layanan->nama_layanan,
                'tipe_layanan' => $layanan->tipe_layanan,
                'berat' => $layanan->tipe_layanan === 'kiloan' ? $quantity : null,
                'jumlah_item' => $layanan->tipe_layanan === 'satuan' ? (int) $quantity : null,
                'satuan_hitung' => $layanan->satuan_hitung,
                'harga_satuan' => $layanan->tarif,
                'subtotal' => $subtotal,
                'catatan' => $data['catatan_pelanggan'] ?? null,
            ]);

            Pembayaran::create([
                'pesanan_id' => $pesanan->id,
                'nomor_pembayaran' => Pembayaran::generateNomorPembayaran(),
                'metode_pembayaran' => 'qris',
                'total_tagihan' => $pesanan->total_biaya,
                'nominal_dibayar' => 0,
                'kembalian' => 0,
                'status_pembayaran' => 'belum_dibayar',
                'tanggal_pembayaran' => null,
                'catatan' => 'Tagihan QRIS otomatis dibuat saat pesanan masuk.',
            ]);

            $pesanan->recordStatusHistory(
                null,
                'menunggu_konfirmasi',
                null,
                'Pesanan dibuat oleh pelanggan dan menunggu konfirmasi admin.'
            );

            return $pesanan->fresh([
                'detailPesanans.layananLaundry',
                'pelanggan',
                'pembayaran',
                'pengingatPengambilan',
            ]);
        });
    }

    public function syncDueReminders(): void
    {
        Pesanan::query()
            ->where('status_pesanan', 'siap_diambil')
            ->whereNotNull('tanggal_siap_diambil')
            ->where('tanggal_siap_diambil', '<=', now()->subDays(3))
            ->with('pengingatPengambilan')
            ->get()
            ->each(fn (Pesanan $pesanan) => $pesanan->syncPengingatPengambilan());
    }

    private function resolvePelanggan(User $user): Pelanggan
    {
        if ($user->pelanggan) {
            return $user->pelanggan;
        }

        return Pelanggan::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'email' => $user->email,
            'nomor_whatsapp' => '-',
            'alamat' => null,
            'status' => 'aktif',
        ]);
    }

    private function normalizeQuantity(LayananLaundry $layanan, array $data): float
    {
        $quantity = $layanan->tipe_layanan === 'kiloan'
            ? (float) ($data['berat'] ?? 0)
            : (int) ($data['jumlah_item'] ?? 0);

        $minimum = (float) ($layanan->minimal_order ?: 1);

        return max($quantity, $minimum);
    }
}