<?php

namespace App\Enums;

enum StatusKrama: string
{
    case Pengampel = 'pengampel';
    case Pemirak = 'pemirak';
    case KramaTamiu = 'krama_tamiu';
    case Tamiu = 'tamiu';

    public function label(): string
    {
        return match ($this) {
            self::Pengampel => 'Pengampel',
            self::Pemirak => 'Pemirak',
            self::KramaTamiu => 'Krama Tamiu',
            self::Tamiu => 'Tamiu',
        };
    }
}
