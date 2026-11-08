<?php

namespace App\Models;

use App\Enums\StatusData;
use App\Enums\StatusKrama;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

#[Fillable([
    'nik', 'nama', 'status', 'status_sebelum_pengampel', 'asal', 'pekerjaan', 'perumahan_id', 'nomor_rumah',
    'jumlah_penghuni', 'tanggal_mulai_menetap', 'latitude', 'longitude', 'lokasi_ditandai_oleh',
    'lokasi_ditandai_at', 'no_hp', 'keterangan', 'impor_baris', 'aktif',
])]
#[Hidden(['nik', 'nik_hash'])]
class Krama extends Model
{
    use SoftDeletes;

    protected $table = 'krama';

    protected $attributes = [
        'status' => 'krama_tamiu',
        'status_data' => 'pending',
        'aktif' => true,
    ];

    protected static function booted(): void
    {
        static::saving(function (Krama $krama) {
            $krama->status_data = $krama->alasanPending() === [] ? StatusData::Lengkap : StatusData::Pending;
        });
    }

    protected function casts(): array
    {
        return [
            'status' => StatusKrama::class,
            'status_sebelum_pengampel' => StatusKrama::class,
            'status_data' => StatusData::class,
            'tanggal_mulai_menetap' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'lokasi_ditandai_at' => 'datetime',
            'aktif' => 'boolean',
        ];
    }

    /**
     * Menyimpan NIK terenkripsi dan mengisi nik_hash untuk cek duplikat.
     */
    protected function nik(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === null ? null : Crypt::decryptString($value),
            set: function (?string $value) {
                $nik = trim((string) $value);

                return $nik === ''
                    ? ['nik' => null, 'nik_hash' => null]
                    : ['nik' => Crypt::encryptString($nik), 'nik_hash' => self::hashNik($nik)];
            },
        );
    }

    public static function hashNik(string $nik): string
    {
        $key = config('sangket.nik_hash_key');

        if (blank($key)) {
            throw new RuntimeException('NIK_HASH_KEY belum diisi.');
        }

        return hash_hmac('sha256', trim($nik), $key);
    }

    /**
     * NIK dianggap valid jika 16 digit dan tanggal lahirnya masuk akal.
     * Untuk perempuan, tanggal lahir di NIK ditambah 40.
     */
    public static function nikValid(?string $nik): bool
    {
        if ($nik === null || ! preg_match('/^\d{16}$/', $nik)) {
            return false;
        }

        $tanggal = (int) substr($nik, 6, 2);
        $bulan = (int) substr($nik, 8, 2);
        $tanggal = $tanggal > 40 ? $tanggal - 40 : $tanggal;

        return $tanggal >= 1 && $tanggal <= 31 && $bulan >= 1 && $bulan <= 12;
    }

    /**
     * @return list<string>
     */
    public function alasanPending(): array
    {
        $alasan = [];

        if ($this->nik === null) {
            $alasan[] = 'NIK kosong';
        } elseif (! self::nikValid($this->nik)) {
            $alasan[] = 'NIK tidak valid';
        }

        if ($this->perumahan_id === null) {
            $alasan[] = 'Perumahan kosong';
        }

        if (blank($this->asal)) {
            $alasan[] = 'Asal kosong';
        }

        return $alasan;
    }

    public function nikTersamar(): ?string
    {
        $nik = $this->nik;

        if ($nik === null) {
            return null;
        }

        return strlen($nik) < 10 ? '****' : substr($nik, 0, 6).str_repeat('*', strlen($nik) - 10).substr($nik, -4);
    }

    public function lokasiLengkap(): bool
    {
        return $this->latitude !== null && $this->longitude !== null && $this->perumahan_id !== null;
    }

    public function tunggakan(): int
    {
        return (int) $this->tagihan()->selectRaw(Tagihan::SQL_TUNGGAKAN.' as jumlah')->value('jumlah');
    }

    public function perumahan(): BelongsTo
    {
        return $this->belongsTo(Perumahan::class);
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }

    public function lokasiDitandaiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lokasi_ditandai_oleh');
    }
}
