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
