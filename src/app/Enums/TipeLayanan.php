<?php

namespace App\Enums;

enum TipeLayanan: string
{
    case Kiloan = 'kiloan';
    case Satuan = 'satuan';

    public function label(): string
    {
        return match ($this) {
            self::Kiloan => 'Kiloan',
            self::Satuan => 'Satuan',
        };
    }
}
