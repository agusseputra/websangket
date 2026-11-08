<?php

namespace App\Models;

use App\Enums\JenisTagihan;
use App\Enums\MetodeBayar;
use App\Enums\StatusTagihan;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'krama_id', 'periode_iuran_id', 'jenis', 'nominal', 'kekurangan_sebelumnya', 'nominal_bayar',
    'tanggal_bayar', 'metode', 'status', 'sisa_dipindah_ke_id', 'no_struk', 'transaksi_uuid',
    'petugas_id', 'divalidasi_oleh', 'divalidasi_at', 'keterangan',
])]
class Tagihan extends Model
{
    protected $table = 'tagihan';

    /**
     * Tunggakan: baris yang belum dibayar, ditambah kurang bayar yang belum pindah ke baris berikutnya.
     * Transfer berstatus pending belum mengurangi tunggakan.
     */
    public const SQL_TUNGGAKAN = "COALESCE(SUM(CASE
        WHEN tagihan.status IN ('dibuat', 'pending') THEN tagihan.total_tagihan
        WHEN tagihan.status IN ('dibayar', 'valid') AND tagihan.sisa_dipindah_ke_id IS NULL
            THEN tagihan.total_tagihan - COALESCE(tagihan.nominal_bayar, 0)
        ELSE 0 END), 0)";

    protected function casts(): array
    {
        return [
            'jenis' => JenisTagihan::class,
            'metode' => MetodeBayar::class,
            'status' => StatusTagihan::class,
            'tanggal_bayar' => 'date',
            'divalidasi_at' => 'datetime',
        ];
    }

    public function krama(): BelongsTo
    {
        return $this->belongsTo(Krama::class);
    }

    public function periodeIuran(): BelongsTo
    {
        return $this->belongsTo(PeriodeIuran::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function sisaDipindahKe(): BelongsTo
    {
        return $this->belongsTo(self::class, 'sisa_dipindah_ke_id');
    }

    public function sisa(): int
    {
        $total = (int) $this->nominal + (int) $this->kekurangan_sebelumnya;

        return in_array($this->status, [StatusTagihan::Dibayar, StatusTagihan::Valid], true)
            ? $total - (int) $this->nominal_bayar
            : ($this->status === StatusTagihan::Dispensasi ? 0 : $total);
    }
}
