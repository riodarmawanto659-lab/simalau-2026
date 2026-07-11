<?php

namespace App\Enums;

enum MetodePenyerahan: string
{
    case AntarSendiri = 'antar_sendiri';
    case Jemput = 'jemput';

    public function label(): string
    {
        return match ($this) {
            self::AntarSendiri => 'Antar Sendiri ke Outlet',
            self::Jemput => 'Minta Dijemput',
        };
    }
}
