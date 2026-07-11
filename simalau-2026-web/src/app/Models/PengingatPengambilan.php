<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengingatPengambilan extends Model
{
    protected $fillable = [
        'pesanan_id',
        'pelanggan_id',
        'tanggal_siap_diambil',
        'tanggal_masuk_pengingat',
        'jumlah_hari_tertahan',
        'status_pengingat',
        'tanggal_dihubungi',
        'catatan',
    ];

    protected $casts = [
        'tanggal_siap_diambil' => 'datetime',
        'tanggal_masuk_pengingat' => 'datetime',
        'tanggal_dihubungi' => 'datetime',
        'jumlah_hari_tertahan' => 'integer',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }


    public function getWhatsappUrlAttribute(): ?string
    {
        $phone = preg_replace('/\D+/', '', (string) $this->pelanggan?->nomor_whatsapp);

        if (! $phone) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $namaPelanggan = $this->pelanggan?->nama_lengkap ?? 'Pelanggan';
        $nomorPesanan = $this->pesanan?->nomor_pesanan ?? '-';

        if ($this->pesanan?->status_pesanan === 'siap_diambil') {
            $message = 'Halo ' . $namaPelanggan
                . ', cucian Anda dengan nomor pesanan ' . $nomorPesanan
                . ' sudah siap diambil. Mohon segera mengambil cucian di outlet LaundryKita. Terima kasih.';
        } else {
            $message = 'Halo ' . $namaPelanggan
                . ', kami dari LaundryKita ingin menghubungi terkait pesanan Anda dengan nomor ' . $nomorPesanan
                . '. Terima kasih.';
        }

        return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
    }

    public function getNamaStatusPengingatAttribute(): string
    {
        return match ($this->status_pengingat) {
            'aktif' => 'Aktif',
            'sudah_dihubungi' => 'Sudah Dihubungi',
            'selesai' => 'Selesai',
            default => '-',
        };
    }
}
