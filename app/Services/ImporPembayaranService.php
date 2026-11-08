<?php

namespace App\Services;

use App\Enums\JenisTagihan;
use App\Enums\MetodeBayar;
use App\Enums\StatusTagihan;
use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class ImporPembayaranService
{
    public const TAHUN = 2026;

    public const TANGGAL_BAYAR = '2026-03-01';

    public const TARIF_DUDUKAN = 200000;

    public const TARIF_PENANJUNG_BATU = 1000000;

    private const SHEET = 'ALL';

    private const BARIS_JUDUL = 3;

    public function pratinjau(string $path): array
    {
        $rencana = $this->rencana($path);
        $dudukan = $rencana['baris']->pluck('dudukan')->filter();
        $penanjung = $rencana['baris']->pluck('penanjung_batu')->filter();

        return [
            'tahun' => self::TAHUN,
            'tanggal_bayar' => self::TANGGAL_BAYAR,
            'krama' => $rencana['baris']->count(),
            'dudukan_bayar' => $dudukan->count(),
            'total_dudukan' => $dudukan->sum(),
            'dispensasi' => $rencana['baris']->where('dispensasi', true)->count(),
            'dudukan_belum_bayar' => $rencana['baris']->filter(fn (array $b) => ! $b['dudukan'] && ! $b['dispensasi'])->count(),
            'penanjung_bayar' => $penanjung->count(),
            'total_penanjung' => $penanjung->sum(),
            'tidak_tertaut' => $rencana['tidak_tertaut'],
            'nama_berbeda' => $rencana['baris']->where('nama_berbeda', true)->pluck('baris')->values()->all(),
        ];
    }

    public function impor(string $path, User $admin): array
    {
        $rencana = $this->rencana($path);

        DB::transaction(function () use ($rencana, $admin) {
            $periode = PeriodeIuran::create([
                'tahun' => self::TAHUN,
                'jumlah_dudukan' => self::TARIF_DUDUKAN,
                'jumlah_penanjung_batu' => self::TARIF_PENANJUNG_BATU,
                'generated_at' => now(),
                'generated_by' => $admin->id,
            ]);

            $rencana['baris']->each(function (array $baris) use ($periode, $admin) {
                $this->buatDudukan($baris, $periode, $admin);

                if ($baris['penanjung_batu']) {
                    $this->buatBaris($baris['krama_id'], $periode, JenisTagihan::PenanjungBatu, self::TARIF_PENANJUNG_BATU, $baris['penanjung_batu'], $admin);
                }
            });
        });

        return $this->pratinjau($path);
    }

    public function sudahDiimpor(): bool
    {
        return PeriodeIuran::where('tahun', self::TAHUN)->exists();
    }

    private function buatDudukan(array $baris, PeriodeIuran $periode, User $admin): void
    {
        if ($baris['dispensasi']) {
            Tagihan::create([
                'krama_id' => $baris['krama_id'],
                'periode_iuran_id' => $periode->id,
                'jenis' => JenisTagihan::Dudukan,
                'nominal' => 0,
                'status' => StatusTagihan::Dispensasi,
                'keterangan' => 'Luput di Excel',
            ]);

            return;
        }

        $this->buatBaris($baris['krama_id'], $periode, JenisTagihan::Dudukan, self::TARIF_DUDUKAN, $baris['dudukan'], $admin);
    }

    private function buatBaris(int $kramaId, PeriodeIuran $periode, JenisTagihan $jenis, int $nominal, ?int $bayar, User $admin): void
    {
        Tagihan::create([
            'krama_id' => $kramaId,
            'periode_iuran_id' => $periode->id,
            'jenis' => $jenis,
            'nominal' => $nominal,
            ...($bayar ? [
                'nominal_bayar' => $bayar,
                'tanggal_bayar' => self::TANGGAL_BAYAR,
                'metode' => MetodeBayar::Tunai,
                'status' => StatusTagihan::Valid,
                'divalidasi_oleh' => $admin->id,
                'divalidasi_at' => now(),
                'keterangan' => 'Impor Excel',
            ] : []),
        ]);
    }

    private function rencana(string $path): array
    {
        $baris = $this->bacaBaris($path);
        $krama = Krama::whereNotNull('impor_baris')->get()->keyBy('impor_baris');

        if ($krama->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'Impor data krama terlebih dahulu.']);
        }

        $tidakTertaut = $baris->keys()->reject(fn (int $nomor) => $krama->has($nomor))->values()->all();

        return [
            'tidak_tertaut' => $tidakTertaut,
            'baris' => $baris->filter(fn (array $b, int $nomor) => $krama->has($nomor))
                ->map(fn (array $b, int $nomor) => [
                    'baris' => $nomor,
                    'krama_id' => $krama[$nomor]->id,
                    'nama_berbeda' => mb_strtolower(trim($krama[$nomor]->nama)) !== mb_strtolower($b['nama']),
                    'dudukan' => $this->rupiah($b['jumlah']),
                    'penanjung_batu' => $this->rupiah($b['penanjung_batu']),
                    'dispensasi' => mb_strtolower($b['keterangan'] ?? '') === 'luput',
                ])->values(),
        ];
    }

    /**
     * Menerima angka maupun teks seperti "Rp 200,000.00" dan "1,000,000".
     */
    private function rupiah(mixed $nilai): ?int
    {
        if ($nilai === null) {
            return null;
        }

        if (is_numeric($nilai)) {
            return (int) round((float) $nilai) ?: null;
        }

        $teks = preg_replace('/[.,]\d{2}$/', '', trim((string) $nilai));

        return (int) preg_replace('/\D/', '', $teks) ?: null;
    }

    private function bacaBaris(string $path): Collection
    {
        $sheet = $this->bukaSheet($path);
        $kolom = $this->petaKolom($sheet);
        $hasil = collect();

        for ($nomor = self::BARIS_JUDUL + 1, $akhir = $sheet->getHighestDataRow(); $nomor <= $akhir; $nomor++) {
            $nama = trim((string) $sheet->getCell([$kolom['nama'], $nomor])->getValue());

            if ($nama === '') {
                continue;
            }

            $hasil[$nomor] = [
                'nama' => mb_strtolower($nama),
                'jumlah' => $this->nilai($sheet, $kolom['jumlah'], $nomor),
                'penanjung_batu' => $this->nilai($sheet, $kolom['penanjung batu'], $nomor),
                'keterangan' => trim((string) $sheet->getCell([$kolom['keterangan'], $nomor])->getValue()) ?: null,
            ];
        }

        return $hasil;
    }

    private function nilai(Worksheet $sheet, int $kolom, int $baris): mixed
    {
        $nilai = $sheet->getCell([$kolom, $baris])->getValue();

        return is_string($nilai) && trim($nilai) === '' ? null : $nilai;
    }

    private function bukaSheet(string $path): Worksheet
    {
        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $reader->setLoadSheetsOnly([self::SHEET]);
            $sheet = $reader->load($path)->getSheetByName(self::SHEET);
        } catch (Throwable) {
            throw ValidationException::withMessages(['file' => 'File tidak bisa dibaca. Pastikan formatnya .xlsx.']);
        }

        if ($sheet === null) {
            throw ValidationException::withMessages(['file' => 'Sheet ALL tidak ditemukan di file ini.']);
        }

        return $sheet;
    }

    private function petaKolom(Worksheet $sheet): array
    {
        $kolom = [];
        $jumlahKolom = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        for ($indeks = 1; $indeks <= $jumlahKolom; $indeks++) {
            $kolom[mb_strtolower(trim((string) $sheet->getCell([$indeks, self::BARIS_JUDUL])->getValue()))] = $indeks;
        }

        foreach (['nama', 'jumlah', 'penanjung batu', 'keterangan'] as $wajib) {
            if (! isset($kolom[$wajib])) {
                throw ValidationException::withMessages(['file' => "Kolom {$wajib} tidak ditemukan di baris 3 sheet ALL."]);
            }
        }

        return $kolom;
    }
}
