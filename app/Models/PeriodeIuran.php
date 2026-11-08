<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['tahun', 'jumlah_dudukan', 'jumlah_penanjung_batu', 'generated_at', 'generated_by'])]
class PeriodeIuran extends Model
{
    protected $table = 'periode_iuran';

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    /**
     * Periode tahun berjalan. Jika belum dibuat, memakai periode dengan tahun terbaru.
     */
    public static function aktif(): ?self
    {
        return self::where('tahun', now()->year)->first() ?? self::orderByDesc('tahun')->first();
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }
}
