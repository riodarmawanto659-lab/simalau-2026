<?php

namespace App\Enums;

enum PesananStatus: string
{
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case MenungguProses = 'menunggu_proses';
    case SedangDicuci = 'sedang_dicuci';
    case SedangDikeringkan = 'sedang_dikeringkan';
    case SedangDisetrika = 'sedang_disetrika';
    case SiapDiambil = 'siap_diambil';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::MenungguProses => 'Menunggu Proses',
            self::SedangDicuci => 'Sedang Dicuci',
            self::SedangDikeringkan => 'Sedang Dikeringkan',
            self::SedangDisetrika => 'Sedang Disetrika',
            self::SiapDiambil => 'Siap Diambil',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public static function flow(): array
    {
        return [
            self::MenungguKonfirmasi,
            self::MenungguProses,
            self::SedangDicuci,
            self::SedangDikeringkan,
            self::SedangDisetrika,
            self::SiapDiambil,
            self::Selesai,
        ];
    }

    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }
}
