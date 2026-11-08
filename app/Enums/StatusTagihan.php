<?php

namespace App\Enums;

enum StatusTagihan: string
{
    case Dibuat = 'dibuat';
    case Dibayar = 'dibayar';
    case Pending = 'pending';
    case Valid = 'valid';
    case Dispensasi = 'dispensasi';
}
