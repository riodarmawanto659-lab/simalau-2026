<?php

namespace App\Enums;

enum PembayaranStatus: string
{
    case BelumDibayar = 'belum_dibayar';
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Lunas = 'lunas';

    public function label(): string
    {
        return match ($this) {
            self::BelumDibayar => 'Belum Dibayar',
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::Lunas => 'Lunas',
        };
    }
}
