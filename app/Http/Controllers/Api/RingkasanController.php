<?php

namespace App\Http\Controllers\Api;

use App\Enums\JenisTagihan;
use App\Enums\Role;
use App\Enums\StatusData;
use App\Enums\StatusLaporan;
use App\Enums\StatusTagihan;
use App\Http\Controllers\Controller;
use App\Models\Krama;
use App\Models\LaporanWarga;
use App\Models\PeriodeIuran;
use App\Models\Perumahan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RingkasanController extends Controller
{
    public function admin(Request $request): JsonResponse
    {
        $tahun = $request->validate(['tahun' => ['nullable', 'integer']])['tahun'] ?? null;
        $periode = $tahun ? PeriodeIuran::where('tahun', $tahun)->first() : PeriodeIuran::aktif();

        return response()->json([
            'krama' => Krama::count(),
            'krama_pending' => Krama::where('status_data', StatusData::Pending)->count(),
            'krama_tanpa_lokasi' => Krama::whereNull('latitude')->count(),
            'perumahan' => Perumahan::count(),
            'petugas' => User::where('role', Role::Petugas)->where('aktif', true)->count(),
            'laporan_pending' => LaporanWarga::where('status', StatusLaporan::Pending)->count(),
            'periode' => $periode ? $this->rekapPeriode($periode) : null,
        ]);
    }

    public function petugas(): JsonResponse
    {
        return response()->json([
            'krama' => Krama::count(),
            'krama_tanpa_lokasi' => Krama::whereNull('latitude')->count(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rekapPeriode(PeriodeIuran $periode): array
    {
        $tagihan = Tagihan::where('periode_iuran_id', $periode->id)->whereHas('krama');

        $perJenis = (clone $tagihan)
            ->selectRaw("jenis,
                SUM(total_tagihan) as tagihan,
                SUM(CASE WHEN status = 'valid' THEN nominal_bayar ELSE 0 END) as valid,
                SUM(CASE WHEN status IN ('dibayar', 'pending') THEN nominal_bayar ELSE 0 END) as belum_dihitung,
                ".Tagihan::SQL_TUNGGAKAN.' as tunggakan')
            ->groupBy('jenis')
            ->get()
            ->keyBy(fn (Tagihan $baris) => $baris->jenis->value);

        $jenis = collect(JenisTagihan::cases())->map(fn (JenisTagihan $satu) => [
            'jenis' => $satu->value,
            'label' => $satu->label(),
            'tagihan' => (int) ($perJenis[$satu->value]->tagihan ?? 0),
            'valid' => (int) ($perJenis[$satu->value]->valid ?? 0),
            'belum_dihitung' => (int) ($perJenis[$satu->value]->belum_dihitung ?? 0),
            'tunggakan' => (int) ($perJenis[$satu->value]->tunggakan ?? 0),
        ]);

        return [
            'tahun' => (int) $periode->tahun,
            'jenis' => $jenis,
            'krama_total' => Krama::count(),
            'krama_bayar_valid' => (clone $tagihan)->where('status', StatusTagihan::Valid)->distinct()->count('krama_id'),
            'tunggakan_total' => $jenis->sum('tunggakan'),
        ];
    }
}
