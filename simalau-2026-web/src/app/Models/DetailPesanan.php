<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DetailPesanan extends Model
{
    protected $fillable = [
        'pesanan_id',
        'layanan_laundry_id',
        'nama_layanan',
        'tipe_layanan',
        'berat',
        'jumlah_item',
        'satuan_hitung',
        'harga_satuan',
        'subtotal',
        'catatan',
        'gambar_item',
    ];

    protected $casts = [
        'berat' => 'decimal:2',
        'jumlah_item' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (DetailPesanan $detailPesanan): void {
            $quantity = $detailPesanan->tipe_layanan === 'kiloan'
                ? (float) $detailPesanan->berat
                : (int) $detailPesanan->jumlah_item;

            $detailPesanan->subtotal = max($quantity, 0) * (float) $detailPesanan->harga_satuan;
        });

        static::saved(function (DetailPesanan $detailPesanan): void {
            $detailPesanan->pesanan?->refreshTotals();
        });

        static::deleted(function (DetailPesanan $detailPesanan): void {
            $detailPesanan->pesanan?->refreshTotals();
        });
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function layananLaundry(): BelongsTo
    {
        return $this->belongsTo(LayananLaundry::class);
    }


    public function getGambarUrlAttribute(): ?string
    {
        if ($this->gambar_item) {
            return Storage::disk('public')->url($this->gambar_item);
        }

        if ($this->layananLaundry?->gambar) {
            return $this->layananLaundry->gambar_url;
        }

        return null;
    }

    public function getHargaSatuanRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->harga_satuan, 0, ',', '.');
    }

    public function getSubtotalRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->subtotal, 0, ',', '.');
    }

    public function getNamaTipeLayananAttribute(): string
    {
        return match ($this->tipe_layanan) {
            'kiloan' => 'Kiloan',
            'satuan' => 'Satuan',
            default => '-',
        };
    }

    public function getJumlahDisplayAttribute(): string
    {
        if ($this->tipe_layanan === 'kiloan') {
            return $this->berat . ' ' . $this->satuan_hitung;
        }

        return $this->jumlah_item . ' ' . $this->satuan_hitung;
    }
}
