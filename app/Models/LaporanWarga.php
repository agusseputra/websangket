<?php

namespace App\Models;

use App\Enums\JenisLaporan;
use App\Enums\StatusLaporan;
use Database\Factories\LaporanWargaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'nama', 'nik', 'asal', 'jenis', 'nama_usaha', 'jenis_usaha', 'alamat', 'tanggal_mulai',
    'tanggal_selesai', 'menetap', 'status', 'ditindak_oleh', 'ditindak_at',
])]
#[Hidden(['nik'])]
class LaporanWarga extends Model
{
    /** @use HasFactory<LaporanWargaFactory> */
    use HasFactory;

    protected $table = 'laporan_warga';

    protected $attributes = [
        'status' => 'pending',
        'menetap' => false,
    ];

    protected function casts(): array
    {
        return [
            'nik' => 'encrypted',
            'jenis' => JenisLaporan::class,
            'status' => StatusLaporan::class,
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'menetap' => 'boolean',
            'ditindak_at' => 'datetime',
        ];
    }

    public function nikTersamar(): string
    {
        return strlen($this->nik) < 10 ? '****' : substr($this->nik, 0, 6).str_repeat('*', strlen($this->nik) - 10).substr($this->nik, -4);
    }

    public function penindak(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditindak_oleh');
    }
}
