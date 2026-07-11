<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    protected $fillable = [
        'nama_laundry',
        'alamat',
        'nomor_whatsapp',
        'email',
        'jam_buka',
        'jam_tutup',
        'deskripsi',
        'catatan_nota',
        'logo',
        'qris_image',
        'latitude',
        'longitude',
        'status_sistem',
    ];

    public function getNamaStatusSistemAttribute(): string
    {
        return match ($this->status_sistem) {
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            default => '-',
        };
    }

    public function getJamOperasionalAttribute(): string
    {
        if (! $this->jam_buka || ! $this->jam_tutup) {
            return '-';
        }

        return $this->jam_buka . ' - ' . $this->jam_tutup;
    }

    public function getQrisImageUrlAttribute(): ?string
    {
        if (! $this->qris_image) {
            return null;
        }

        return asset('storage/' . $this->qris_image);
    }
}
