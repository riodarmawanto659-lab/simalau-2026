<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LayananLaundry extends Model
{
    protected $fillable = [
        'kategori_layanan_id',
        'nama_layanan',
        'slug',
        'deskripsi',
        'tipe_layanan',
        'tarif',
        'estimasi_hari',
        'minimal_order',
        'satuan_hitung',
        'status',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
        'estimasi_hari' => 'integer',
        'minimal_order' => 'integer',
    ];

    public function kategoriLayanan(): BelongsTo
    {
        return $this->belongsTo(KategoriLayanan::class);
    }

    public function detailPesanans(): HasMany
    {
        return $this->hasMany(DetailPesanan::class);
    }

    public function getTarifRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->tarif, 0, ',', '.');
    }

    public function getNamaStatusAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            default => '-',
        };
    }

    public function getNamaTipeLayananAttribute(): string
    {
        return match ($this->tipe_layanan) {
            'kiloan' => 'Kiloan',
            'satuan' => 'Satuan',
            default => '-',
        };
    }
}