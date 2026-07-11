<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $fillable = [
        'nama_hari_libur',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];


    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSedangBerlangsung(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->aktif()
            ->whereDate('tanggal_mulai', '<=', $today)
            ->where(function (Builder $query) use ($today): void {
                $query->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', $today);
            });
    }

    public function scopeMendatang(Builder $query): Builder
    {
        return $query->aktif()
            ->whereDate('tanggal_mulai', '>', now()->toDateString());
    }

    public function getNamaJenisAttribute(): string
    {
        return match ($this->jenis) {
            'nasional' => 'Nasional',
            'operasional' => 'Operasional',
            'lainnya' => 'Lainnya',
            default => '-',
        };
    }

    public function getNamaStatusAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif',
            default => '-',
        };
    }

    public function getPeriodeLiburAttribute(): string
    {
        if (! $this->tanggal_mulai) {
            return '-';
        }

        if (! $this->tanggal_selesai) {
            return $this->tanggal_mulai->format('d/m/Y');
        }

        return $this->tanggal_mulai->format('d/m/Y') . ' - ' . $this->tanggal_selesai->format('d/m/Y');
    }
}