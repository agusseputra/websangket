<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeriodeIuran;
use App\Models\Tagihan;
use App\Services\GenerateTagihanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PeriodeIuranController extends Controller
{
    private const TARIF_AWAL = ['jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000];

    public function __construct(private GenerateTagihanService $generate) {}

    public function index(): JsonResponse
    {
        $rekap = Tagihan::query()
            ->selectRaw("periode_iuran_id, jenis, COUNT(*) as baris,
                SUM(CASE WHEN status = 'valid' THEN nominal_bayar ELSE 0 END) as valid,
                SUM(CASE WHEN status = 'dibayar' THEN nominal_bayar ELSE 0 END) as dibayar,
                SUM(CASE WHEN status = 'pending' THEN nominal_bayar ELSE 0 END) as pending,
                ".Tagihan::SQL_TUNGGAKAN.' as tunggakan')
            ->groupBy('periode_iuran_id', 'jenis')
            ->get()
            ->groupBy('periode_iuran_id');

        $terakhir = PeriodeIuran::orderByDesc('tahun')->first();

        return response()->json([
            'tarif_awal' => $terakhir
                ? ['jumlah_dudukan' => (int) $terakhir->jumlah_dudukan, 'jumlah_penanjung_batu' => (int) $terakhir->jumlah_penanjung_batu]
                : self::TARIF_AWAL,
            'data' => PeriodeIuran::orderByDesc('tahun')->get()->map(fn (PeriodeIuran $periode) => [
                'id' => $periode->id,
                'tahun' => (int) $periode->tahun,
                'jumlah_dudukan' => (int) $periode->jumlah_dudukan,
                'jumlah_penanjung_batu' => (int) $periode->jumlah_penanjung_batu,
                'generated_at' => $periode->generated_at?->toDateTimeString(),
                'rekap' => ($rekap[$periode->id] ?? collect())->map(fn ($baris) => [
                    'jenis' => $baris->jenis->value,
                    'jenis_label' => $baris->jenis->label(),
                    'baris' => (int) $baris->baris,
                    'valid' => (int) $baris->valid,
                    'dibayar' => (int) $baris->dibayar,
                    'pending' => (int) $baris->pending,
                    'tunggakan' => (int) $baris->tunggakan,
                ])->values(),
            ]),
        ]);
    }

    public function tahun(): JsonResponse
    {
        return response()->json(PeriodeIuran::orderByDesc('tahun')->pluck('tahun')->map(fn ($tahun) => (int) $tahun));
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(PeriodeIuran::create($this->validasi($request)), 201);
    }

    public function update(Request $request, PeriodeIuran $periode): JsonResponse
    {
        $data = $this->validasi($request, $periode);

        // Tahun tidak boleh berubah setelah ada tagihan, agar riwayat kartu tetap benar.
        if ((int) $data['tahun'] !== (int) $periode->tahun && $periode->tagihan()->exists()) {
            throw ValidationException::withMessages(['tahun' => 'Tahun tidak bisa diubah karena periode ini sudah punya tagihan.']);
        }

        $periode->update($data);

        return response()->json($periode);
    }

    public function pratinjau(PeriodeIuran $periode): JsonResponse
    {
        return response()->json($this->generate->pratinjau($periode));
    }

    public function generate(Request $request, PeriodeIuran $periode): JsonResponse
    {
        return response()->json($this->generate->generate($periode, $request->user()));
    }

    private function validasi(Request $request, ?PeriodeIuran $periode = null): array
    {
        return $request->validate([
            'tahun' => ['required', 'integer', 'between:2020,2100', Rule::unique('periode_iuran', 'tahun')->ignore($periode)],
            'jumlah_dudukan' => ['required', 'integer', 'min:0', 'max:999999999999'],
            'jumlah_penanjung_batu' => ['required', 'integer', 'min:0', 'max:999999999999'],
        ], [
            'tahun.required' => 'Tahun wajib diisi.',
            'tahun.unique' => 'Periode tahun ini sudah ada.',
            'jumlah_dudukan.required' => 'Tarif Dudukan wajib diisi.',
            'jumlah_penanjung_batu.required' => 'Tarif Penanjung Batu wajib diisi.',
        ]);
    }
}
