<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArusKas extends Model
{
    protected $table = 'arus_kas';

    protected $fillable = [
        'pesanan_id',
        'pembayaran_id',
        'user_id',
        'jenis',
        'kategori',
        'judul',
        'nominal',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal' => 'date',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNominalRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->nominal, 0, ',', '.');
    }

    public function getNamaJenisAttribute(): string
    {
        return match ($this->jenis) {
            'masuk' => 'Kas Masuk',
            'keluar' => 'Kas Keluar',
            default => '-',
        };
    }
}