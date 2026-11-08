<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'latitude', 'longitude'])]
class Perumahan extends Model
{
    protected $table = 'perumahan';

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function krama(): HasMany
    {
        return $this->hasMany(Krama::class);
    }
}
