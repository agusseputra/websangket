<?php

namespace App\Enums;

enum JenisTagihan: string
{
    case Dudukan = 'dudukan';
    case PenanjungBatu = 'penanjung_batu';

    public function label(): string
    {
        return match ($this) {
            self::Dudukan => 'Dudukan',
            self::PenanjungBatu => 'Penanjung Batu',
        };
    }
}
