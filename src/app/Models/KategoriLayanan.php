<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriLayanan extends Model
{
    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
        'status',
        'urutan',
    ];

    public function layananLaundries(): HasMany
    {
        return $this->hasMany(LayananLaundry::class);
    }

    public function getNamaStatusAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            default => '-',
        };
    }
}