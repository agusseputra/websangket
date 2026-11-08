<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Krama;
use App\Models\Tagihan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CekTunggakanController extends Controller
{
    /**
     * Pengecekan publik tanpa login: data hanya tampil jika nama dan NIK cocok pada krama yang sama.
     * NIK, no HP, dan koordinat tidak pernah dikirim.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nik' => ['required', 'string', 'regex:/^\d{10,20}$/'],
        ], [
            'nik.regex' => 'NIK hanya berisi angka, biasanya 16 digit.',
        ]);

        $krama = Krama::with(['perumahan', 'tagihan.periodeIuran'])
            ->where('nik_hash', Krama::hashNik($data['nik']))
            ->where('aktif', true)
            ->first();

        if (! $krama || $this->normalkanNama($krama->nama) !== $this->normalkanNama($data['nama'])) {
            return response()->json([
                'message' => 'Data tidak ditemukan. Pastikan nama dan NIK sama dengan yang terdaftar.',
            ], 404);
        }

        return response()->json([
            'nama' => $krama->nama,
            'status' => $krama->status->label(),
            'perumahan' => $krama->perumahan?->nama,
            'nomor_rumah' => $krama->nomor_rumah,
            'tunggakan' => $krama->tunggakan(),
            'riwayat' => $krama->tagihan
                ->sortByDesc(fn (Tagihan $baris) => [(int) $baris->periodeIuran->tahun, $baris->id])
                ->values()
                ->map(fn (Tagihan $baris) => [
                    'id' => $baris->id,
                    'tahun' => (int) $baris->periodeIuran->tahun,
                    'jenis' => $baris->jenis->label(),
                    'total_tagihan' => (int) $baris->nominal + (int) $baris->kekurangan_sebelumnya,
                    'nominal_bayar' => $baris->nominal_bayar === null ? null : (int) $baris->nominal_bayar,
                    'tanggal_bayar' => $baris->tanggal_bayar?->toDateString(),
                    'status' => $baris->status->value,
                ]),
        ]);
    }

    private function normalkanNama(string $nama): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($nama)));
    }
}
