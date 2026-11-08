<?php

namespace App\Enums;

enum JenisLaporan: string
{
    case Tinggal = 'tinggal';
    case Usaha = 'usaha';

    public function label(): string
    {
        return match ($this) {
            self::Tinggal => 'Tinggal',
            self::Usaha => 'Usaha',
        };
    }
}
