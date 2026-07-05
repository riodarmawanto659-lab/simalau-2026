<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    protected $fillable = [
        'pelanggan_id',
        'nomor_pesanan',
        'tanggal_masuk',
        'estimasi_selesai',
        'tanggal_siap_diambil',
        'tanggal_selesai',
        'metode_penyerahan',
        'alamat_penjemputan',
        'catatan_pelanggan',
        'catatan_admin',
        'status_pesanan',
        'status_pembayaran',
        'subtotal',
        'diskon',
        'total_biaya',
        'urutan_antrian',
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'estimasi_selesai' => 'datetime',
        'tanggal_siap_diambil' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'urutan_antrian' => 'integer',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function detailPesanans(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function riwayatStatuses(): HasMany
    {
        return $this->hasMany(RiwayatStatus::class);
    }

    public function pengingatPengambilan(): HasOne
    {
        return $this->hasOne(PengingatPengambilan::class);
    }

    public function arusKas(): HasMany
    {
        return $this->hasMany(ArusKas::class);
    }

    public function getTotalBiayaRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_biaya, 0, ',', '.');
    }

    public function getNamaStatusPesananAttribute(): string
    {
        return match ($this->status_pesanan) {
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'menunggu_proses' => 'Menunggu Proses',
            'sedang_dicuci' => 'Sedang Dicuci',
            'sedang_dikeringkan' => 'Sedang Dikeringkan',
            'sedang_disetrika' => 'Sedang Disetrika',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => '-',
        };
    }

    public function getNamaStatusPembayaranAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'belum_dibayar' => 'Belum Dibayar',
            'lunas' => 'Lunas',
            default => '-',
        };
    }

    public function getNamaMetodePenyerahanAttribute(): string
    {
        return match ($this->metode_penyerahan) {
            'antar_sendiri' => 'Antar Sendiri ke Outlet',
            'jemput' => 'Minta Dijemput',
            default => '-',
        };
    }
}