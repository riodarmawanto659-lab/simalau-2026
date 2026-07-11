<?php

namespace App\Enums;

enum JenisArusKas: string
{
    case Masuk = 'masuk';
    case Keluar = 'keluar';

    public function label(): string
    {
        return match ($this) {
            self::Masuk => 'Kas Masuk',
            self::Keluar => 'Kas Keluar',
        };
    }
}
