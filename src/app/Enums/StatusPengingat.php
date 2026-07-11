<?php

namespace App\Enums;

enum StatusPengingat: string
{
    case Aktif = 'aktif';
    case SudahDihubungi = 'sudah_dihubungi';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::SudahDihubungi => 'Sudah Dihubungi',
            self::Selesai => 'Selesai',
        };
    }
}
