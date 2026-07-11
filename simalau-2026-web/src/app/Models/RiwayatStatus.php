<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatStatus extends Model
{
    protected $fillable = [
        'pesanan_id',
        'user_id',
        'status_sebelumnya',
        'status_baru',
        'tanggal_perubahan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_perubahan' => 'datetime',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
