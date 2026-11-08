<?php

namespace App\Services;

use App\Enums\StatusKrama;
use App\Models\Krama;
use App\Models\Perumahan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class ImporKramaService
{
    private const SHEET = 'ALL';

    private const BARIS_JUDUL = 3;

    private const KOLOM_WAJIB = ['Nama', 'Status', 'NIK', 'Asal', 'Perumahan', 'Nomor', 'No HP', 'Keterangan'];

    private const STATUS = [
        'pengampel' => StatusKrama::Pengampel,
        'pemirak' => StatusKrama::Pemirak,
        'krama tamiu' => StatusKrama::KramaTamiu,
        'tamiu' => StatusKrama::Tamiu,
    ];

    // Koreksi salah ketik dan nilai default yang sudah disepakati.
    private const KOREKSI_PERUMAHAN = [
        'bukti awangga' => 'Bukit Awangga',
        'griya adi jaya' => 'Griya Adi Jaya 1',
        'krisna graha' => 'Krisna Graha 1',
    ];

    private const PEKERJAAN = ['swasta', 'pns', 'tentara', 'pensiunan', 'mahasiswa'];

    /**
     * Membaca file dan menyusun laporan tanpa menulis apa pun ke database.
     */
    public function pratinjau(string $path): array
    {
        $baris = $this->bacaBaris($path);
        $perumahan = $this->daftarPerumahan();

        $data = $baris->map(fn (array $kolom, int $nomor) => $this->olahBaris($kolom, $nomor, $perumahan));

        $masalah = $data->flatMap(fn (array $satu) => collect($satu['masalah'])->map(fn (string $pesan) => [
            'baris' => $satu['atribut']['impor_baris'],
            'nama' => $satu['atribut']['nama'],
            'masalah' => $pesan,
        ]))->values();

        $hitung = fn (string $awalan) => $masalah->filter(fn (array $m) => str_starts_with($m['masalah'], $awalan))->count();
        $pending = $data->filter(fn (array $satu) => $satu['pending'])->count();

        return [
            'ringkasan' => [
                'total' => $data->count(),
                'lengkap' => $data->count() - $pending,
                'pending' => $pending,
                'nik_kosong' => $hitung('NIK kosong'),
                'nik_tidak_valid' => $hitung('NIK tidak valid'),
                'asal_kosong' => $hitung('Asal kosong'),
                'perumahan_kosong' => $hitung('Perumahan kosong'),
                'perumahan_tidak_cocok' => $hitung('Perumahan tidak ada'),
                'perumahan_dikoreksi' => $hitung('Perumahan dikoreksi'),
                'status_kosong' => $hitung('Status kosong'),
            ],
            'masalah' => $masalah->all(),
        ];
    }

    public function impor(string $path): array
    {
        $baris = $this->bacaBaris($path);
        $perumahan = $this->daftarPerumahan();

        DB::transaction(function () use ($baris, $perumahan) {
            $baris->each(function (array $kolom, int $nomor) use ($perumahan) {
                Krama::create($this->olahBaris($kolom, $nomor, $perumahan)['atribut']);
            });
        });

        return [
            'total' => $baris->count(),
            'lengkap' => Krama::whereNotNull('impor_baris')->where('status_data', 'lengkap')->count(),
            'pending' => Krama::whereNotNull('impor_baris')->where('status_data', 'pending')->count(),
        ];
    }

    private function olahBaris(array $kolom, int $nomor, Collection $perumahan): array
    {
        $masalah = [];
        $catatan = [];

        $nik = $kolom['NIK'] ?? null;

        if ($nik === null) {
            $masalah[] = 'NIK kosong';
        } elseif (! Krama::nikValid($nik)) {
            $masalah[] = 'NIK tidak valid';
        }

        $asal = $kolom['Asal'] ?? null;

        if ($asal === null) {
            $masalah[] = 'Asal kosong';
        }

        $status = self::STATUS[mb_strtolower($kolom['Status'] ?? '')] ?? null;

        if ($status === null) {
            $masalah[] = 'Status kosong, diisi Krama Tamiu';
            $status = StatusKrama::KramaTamiu;
        }

        $perumahanId = null;
        $namaPerumahan = $kolom['Perumahan'] ?? null;

        if ($namaPerumahan === null) {
            $masalah[] = 'Perumahan kosong';
        } else {
            $kunci = mb_strtolower($namaPerumahan);
            $koreksi = self::KOREKSI_PERUMAHAN[$kunci] ?? null;
            $perumahanId = $perumahan[mb_strtolower($koreksi ?? $namaPerumahan)] ?? null;

            if ($perumahanId === null) {
                $masalah[] = "Perumahan tidak ada di Referensi: {$namaPerumahan}";
                $catatan[] = "Perumahan di Excel: {$namaPerumahan}";
            } elseif ($koreksi !== null) {
                $masalah[] = "Perumahan dikoreksi: {$namaPerumahan} menjadi {$koreksi}";
            }
        }

        $pekerjaan = null;
        $keterangan = $kolom['Keterangan'] ?? null;

        if ($keterangan !== null && in_array(mb_strtolower($keterangan), self::PEKERJAAN, true)) {
            $pekerjaan = $keterangan;
            $keterangan = null;
        }

        array_unshift($catatan, ...array_filter([$keterangan, $kolom['_catatan'] ?? null]));

        return [
            'atribut' => [
                'impor_baris' => $nomor,
                'nama' => $kolom['Nama'],
                'nik' => $nik,
                'status' => $status,
                'asal' => $asal,
                'pekerjaan' => $pekerjaan,
                'perumahan_id' => $perumahanId,
                'nomor_rumah' => $kolom['Nomor'] ?? null,
                'no_hp' => $kolom['No HP'] ?? null,
                'keterangan' => $catatan === [] ? null : implode('; ', $catatan),
            ],
            'masalah' => $masalah,
            'pending' => $nik === null || ! Krama::nikValid($nik) || $asal === null || $perumahanId === null,
        ];
    }

    /**
     * @return Collection<int, array<string, string>> Baris sheet, dengan nomor baris sebagai kunci.
     */
    private function bacaBaris(string $path): Collection
    {
        $sheet = $this->bukaSheet($path);
        $judul = $this->petaJudul($sheet);
        $barisTerakhir = $sheet->getHighestDataRow();
        $hasil = collect();

        for ($nomor = self::BARIS_JUDUL + 1; $nomor <= $barisTerakhir; $nomor++) {
            $kolom = [];
            $catatan = [];

            foreach ($judul as $indeks => $nama) {
                $nilai = $this->nilaiSel($sheet, $indeks, $nomor);

                if ($nilai === null) {
                    continue;
                }

                if ($nama === null) {
                    $catatan[] = $nilai;
                } else {
                    $kolom[$nama] = $nilai;
                }
            }

            if (($kolom['Nama'] ?? null) === null) {
                continue;
            }

            $kolom['_catatan'] = $catatan === [] ? null : implode('; ', $catatan);
            $hasil[$nomor] = $kolom;
        }

        if ($hasil->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'Tidak ada baris krama yang bisa dibaca di sheet ALL.']);
        }

        return $hasil;
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

    /**
     * @return array<int, string|null> Indeks kolom ke nama judul. Kolom tanpa judul berisi catatan.
     */
    private function petaJudul(Worksheet $sheet): array
    {
        $jumlahKolom = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());
        $abaikan = ['no', 'cacakan', 'jumlah', 'penanjung batu'];
        $wajib = collect(self::KOLOM_WAJIB)->keyBy(fn (string $nama) => mb_strtolower($nama));
        $judul = [];

        for ($indeks = 1; $indeks <= $jumlahKolom; $indeks++) {
            $nilai = mb_strtolower($this->nilaiSel($sheet, $indeks, self::BARIS_JUDUL) ?? '');

            if (in_array($nilai, $abaikan, true)) {
                continue;
            }

            $judul[$indeks] = $wajib[$nilai] ?? null;
        }

        $kurang = array_diff(self::KOLOM_WAJIB, array_filter($judul));

        if ($kurang !== []) {
            throw ValidationException::withMessages([
                'file' => 'Kolom berikut tidak ditemukan di baris 3 sheet ALL: '.implode(', ', $kurang).'.',
            ]);
        }

        return $judul;
    }

    private function nilaiSel(Worksheet $sheet, int $kolom, int $baris): ?string
    {
        $nilai = $sheet->getCell([$kolom, $baris])->getValue();

        if (is_float($nilai) && floor($nilai) === $nilai) {
            $nilai = number_format($nilai, 0, '', '');
        }

        $teks = trim((string) $nilai);

        return $teks === '' ? null : $teks;
    }

    /**
     * @return Collection<string, int>
     */
    private function daftarPerumahan(): Collection
    {
        return Perumahan::pluck('id', 'nama')->mapWithKeys(fn (int $id, string $nama) => [mb_strtolower($nama) => $id]);
    }
}
