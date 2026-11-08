<?php

namespace App\Services;

use App\Enums\JenisTagihan;
use App\Enums\StatusKrama;
use App\Enums\StatusTagihan;
use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GenerateTagihanService
{
    // Penanjung Batu pertama kali ditagihkan pada Generate 2027.
    public const PENANJUNG_BATU_MULAI = 2027;

    public function pratinjau(PeriodeIuran $periode): array
    {
        $hasil = ['krama' => 0, 'dudukan' => 0, 'penanjung_batu' => 0, 'total_dudukan' => 0, 'total_penanjung_batu' => 0];

        $this->rencana($periode)->each(function (array $rencana) use (&$hasil) {
            $hasil['krama']++;

            foreach ($rencana['baris'] as $baris) {
                $jenis = $baris['jenis']->value;
                $hasil[$jenis]++;
                $hasil["total_{$jenis}"] += $baris['nominal'] + $baris['kekurangan_sebelumnya'];
            }
        });

        return $hasil;
    }

    public function generate(PeriodeIuran $periode, User $admin): array
    {
        return DB::transaction(function () use ($periode, $admin) {
            $hasil = $this->pratinjau($periode);

            $this->rencana($periode)->each(function (array $rencana) use ($periode) {
                foreach ($rencana['baris'] as $baris) {
                    $this->simpanBaris($rencana['krama_id'], $baris, $periode);
                }
            });

            $periode->update(['generated_at' => now(), 'generated_by' => $admin->id]);

            return $hasil;
        });
    }

    /**
     * Membuat tagihan satu krama yang belum ada pada periode ini, dengan aturan yang sama seperti Generate.
     *
     * @return Collection<int, Tagihan>
     */
    public function untukKrama(Krama $krama, PeriodeIuran $periode): Collection
    {
        return DB::transaction(function () use ($krama, $periode) {
            // Mencegah tagihan ganda saat halaman bayar dibuka bersamaan.
            $terkunci = Krama::whereKey($krama->id)->lockForUpdate()->with('tagihan.periodeIuran')->firstOrFail();

            return collect($this->barisKrama($terkunci, $periode))
                ->map(fn (array $baris) => $this->simpanBaris($terkunci->id, $baris, $periode))
                ->values();
        });
    }

    private function simpanBaris(int $kramaId, array $baris, PeriodeIuran $periode): Tagihan
    {
        $tagihan = Tagihan::create([
            'krama_id' => $kramaId,
            'periode_iuran_id' => $periode->id,
            'jenis' => $baris['jenis'],
            'nominal' => $baris['nominal'],
            'kekurangan_sebelumnya' => $baris['kekurangan_sebelumnya'],
        ]);

        Tagihan::whereIn('id', $baris['sumber_sisa'])->update(['sisa_dipindah_ke_id' => $tagihan->id]);

        return $tagihan;
    }

    /**
     * Menyusun baris yang belum ada, sehingga Generate aman dijalankan berulang.
     *
     * @return Collection<int, array{krama_id: int, baris: list<array>}>
     */
    private function rencana(PeriodeIuran $periode): Collection
    {
        return Krama::where('aktif', true)
            ->with('tagihan.periodeIuran')
            ->get()
            ->map(fn (Krama $krama) => [
                'krama_id' => $krama->id,
                'baris' => $this->barisKrama($krama, $periode),
            ])
            ->filter(fn (array $rencana) => $rencana['baris'] !== [])
            ->values();
    }

    private function barisKrama(Krama $krama, PeriodeIuran $periode): array
    {
        return array_values(array_filter([
            $this->barisDudukan($krama, $periode),
            $this->barisPenanjungBatu($krama, $periode),
        ]));
    }

    private function barisDudukan(Krama $krama, PeriodeIuran $periode): ?array
    {
        $tagihan = $krama->tagihan->where('jenis', JenisTagihan::Dudukan);

        if ($tagihan->contains('periode_iuran_id', $periode->id)) {
            return null;
        }

        $sisa = $this->barisKurangBayar($tagihan, $periode);

        return [
            'jenis' => JenisTagihan::Dudukan,
            'nominal' => (int) $periode->jumlah_dudukan,
            'kekurangan_sebelumnya' => $sisa->sum(fn (Tagihan $baris) => $baris->sisa()),
            'sumber_sisa' => $sisa->pluck('id')->all(),
        ];
    }

    private function barisPenanjungBatu(Krama $krama, PeriodeIuran $periode): ?array
    {
        $tagihan = $krama->tagihan->where('jenis', JenisTagihan::PenanjungBatu);

        if ((int) $periode->tahun < self::PENANJUNG_BATU_MULAI || $tagihan->contains('periode_iuran_id', $periode->id)) {
            return null;
        }

        if ($tagihan->isEmpty()) {
            if (! in_array($krama->status, [StatusKrama::KramaTamiu, StatusKrama::Tamiu], true)) {
                return null;
            }

            return [
                'jenis' => JenisTagihan::PenanjungBatu,
                'nominal' => (int) $periode->jumlah_penanjung_batu,
                'kekurangan_sebelumnya' => 0,
                'sumber_sisa' => [],
            ];
        }

        $sisa = $this->barisKurangBayar($tagihan, $periode);

        if ($sisa->isEmpty()) {
            return null;
        }

        return [
            'jenis' => JenisTagihan::PenanjungBatu,
            'nominal' => $sisa->sum(fn (Tagihan $baris) => $baris->sisa()),
            'kekurangan_sebelumnya' => 0,
            'sumber_sisa' => $sisa->pluck('id')->all(),
        ];
    }

    private function barisKurangBayar(Collection $tagihan, PeriodeIuran $periode): Collection
    {
        return $tagihan->filter(fn (Tagihan $baris) => in_array($baris->status, [StatusTagihan::Dibayar, StatusTagihan::Valid], true)
            && $baris->sisa_dipindah_ke_id === null
            && $baris->sisa() > 0
            && (int) $baris->periodeIuran->tahun < (int) $periode->tahun);
    }
}
