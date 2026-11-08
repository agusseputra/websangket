<?php

namespace App\Services;

use App\Enums\JenisTagihan;
use App\Enums\MetodeBayar;
use App\Enums\StatusKrama;
use App\Enums\StatusTagihan;
use App\Models\Krama;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembayaranService
{
    /**
     * @param  array<int, int>  $nominal  ID tagihan ke nominal yang dibayar.
     * @return Collection<int, Tagihan>
     */
    public function bayar(Krama $krama, array $nominal, MetodeBayar $metode, string $uuid, User $petugas): Collection
    {
        return DB::transaction(function () use ($krama, $nominal, $metode, $uuid, $petugas) {
            // Pengiriman ulang saat sinyal putus mengembalikan transaksi yang sudah tercatat.
            $sudahAda = Tagihan::where('transaksi_uuid', $uuid)->where('krama_id', $krama->id)->get();

            if ($sudahAda->isNotEmpty()) {
                return $sudahAda;
            }

            if (! $krama->lokasiLengkap()) {
                throw ValidationException::withMessages([
                    'lokasi' => 'Tandai lokasi dan pilih perumahan krama sebelum menerima pembayaran.',
                ]);
            }

            $tagihan = Tagihan::whereIn('id', array_keys($nominal))
                ->where('krama_id', $krama->id)
                ->lockForUpdate()
                ->get();

            if ($tagihan->count() !== count($nominal)) {
                throw ValidationException::withMessages(['tagihan' => 'Ada tagihan yang bukan milik krama ini.']);
            }

            $noStruk = 'DS-'.now()->format('ymd').'-'.strtoupper(substr($uuid, 0, 6));

            $hasil = $tagihan->map(function (Tagihan $baris) use ($nominal, $metode, $uuid, $petugas, $noStruk) {
                if ($baris->status !== StatusTagihan::Dibuat) {
                    throw ValidationException::withMessages(['tagihan' => 'Ada tagihan yang sudah dibayar. Muat ulang daftar tagihan.']);
                }

                $bayar = $nominal[$baris->id];
                $total = (int) $baris->nominal + (int) $baris->kekurangan_sebelumnya;

                if ($bayar > $total) {
                    throw ValidationException::withMessages(['tagihan' => 'Nominal bayar melebihi total tagihan.']);
                }

                $baris->update([
                    'nominal_bayar' => $bayar,
                    'tanggal_bayar' => now()->toDateString(),
                    'metode' => $metode,
                    'status' => $metode === MetodeBayar::Tunai ? StatusTagihan::Dibayar : StatusTagihan::Pending,
                    'no_struk' => $noStruk,
                    'transaksi_uuid' => $uuid,
                    'petugas_id' => $petugas->id,
                ]);

                return $baris;
            });

            $this->perbaruiStatusKramaDari($hasil);

            return $hasil;
        });
    }

    /**
     * @param  list<int>  $ids
     */
    public function validasi(array $ids, User $admin): int
    {
        return DB::transaction(function () use ($ids, $admin) {
            $tagihan = Tagihan::whereIn('id', $ids)
                ->whereIn('status', [StatusTagihan::Dibayar, StatusTagihan::Pending])
                ->lockForUpdate()
                ->get();

            $tagihan->each(fn (Tagihan $baris) => $baris->update([
                'status' => StatusTagihan::Valid,
                'divalidasi_oleh' => $admin->id,
                'divalidasi_at' => now(),
            ]));

            $this->perbaruiStatusKramaDari($tagihan);

            return $tagihan->count();
        });
    }

    public function koreksi(Tagihan $tagihan, string $alasan): void
    {
        if (! in_array($tagihan->status, [StatusTagihan::Dibayar, StatusTagihan::Pending], true)) {
            throw ValidationException::withMessages(['tagihan' => 'Hanya pembayaran yang belum valid yang bisa dikoreksi.']);
        }

        DB::transaction(function () use ($tagihan, $alasan) {
            $tagihan->update([
                'nominal_bayar' => null,
                'tanggal_bayar' => null,
                'metode' => null,
                'status' => StatusTagihan::Dibuat,
                'no_struk' => null,
                'transaksi_uuid' => null,
                'petugas_id' => null,
                'keterangan' => $this->tambahCatatan($tagihan, "Koreksi: {$alasan}"),
            ]);

            if ($tagihan->jenis === JenisTagihan::PenanjungBatu) {
                $this->pulihkanStatusKrama($tagihan->krama);
            }
        });
    }

    public function dispensasi(Tagihan $tagihan, string $alasan): void
    {
        if ($tagihan->status !== StatusTagihan::Dibuat) {
            throw ValidationException::withMessages(['tagihan' => 'Dispensasi hanya untuk tagihan yang belum dibayar.']);
        }

        $tagihan->update([
            'nominal' => 0,
            'kekurangan_sebelumnya' => 0,
            'status' => StatusTagihan::Dispensasi,
            'keterangan' => $this->tambahCatatan($tagihan, "Dispensasi: {$alasan}"),
        ]);
    }

    /**
     * @param  Collection<int, Tagihan>  $tagihan
     */
    private function perbaruiStatusKramaDari(Collection $tagihan): void
    {
        $tagihan->where('jenis', JenisTagihan::PenanjungBatu)
            ->pluck('krama_id')
            ->unique()
            ->each(fn (int $kramaId) => $this->perbaruiStatusKrama(Krama::findOrFail($kramaId)));
    }

    /**
     * Krama Tamiu dan Tamiu menjadi Pengampel setelah Penanjung Batu lunas dan berstatus dibayar atau valid.
     * Transfer yang masih pending baru dihitung setelah divalidasi.
     */
    private function perbaruiStatusKrama(Krama $krama): void
    {
        if (! in_array($krama->status, [StatusKrama::KramaTamiu, StatusKrama::Tamiu], true)) {
            return;
        }

        $tagihan = $krama->tagihan()->where('jenis', JenisTagihan::PenanjungBatu)->get();

        $lunas = $tagihan->isNotEmpty() && $tagihan->every(
            fn (Tagihan $baris) => in_array($baris->status, [StatusTagihan::Dibayar, StatusTagihan::Valid], true)
                && ($baris->sisa() === 0 || $baris->sisa_dipindah_ke_id !== null),
        );

        if ($lunas) {
            $krama->update(['status_sebelum_pengampel' => $krama->status, 'status' => StatusKrama::Pengampel]);
        }
    }

    private function pulihkanStatusKrama(Krama $krama): void
    {
        if ($krama->status === StatusKrama::Pengampel && $krama->status_sebelum_pengampel !== null) {
            $krama->update(['status' => $krama->status_sebelum_pengampel, 'status_sebelum_pengampel' => null]);
        }
    }

    private function tambahCatatan(Tagihan $tagihan, string $catatan): string
    {
        return collect([$tagihan->keterangan, $catatan])->filter()->implode('; ');
    }
}
