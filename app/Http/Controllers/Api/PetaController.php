<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Krama;
use App\Models\Perumahan;
use Illuminate\Http\JsonResponse;

class PetaController extends Controller
{
    /**
     * Titik krama aktif dan perumahan yang sudah punya koordinat. NIK tidak ikut dikirim.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'krama' => Krama::with('perumahan')
                ->where('aktif', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('nama')
                ->get()
                ->map(fn (Krama $krama) => [
                    'id' => $krama->id,
                    'nama' => $krama->nama,
                    'status' => $krama->status->value,
                    'status_label' => $krama->status->label(),
                    'perumahan' => $krama->perumahan?->nama,
                    'nomor_rumah' => $krama->nomor_rumah,
                    'latitude' => (float) $krama->latitude,
                    'longitude' => (float) $krama->longitude,
                ]),
            'perumahan' => Perumahan::withCount('krama')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('nama')
                ->get()
                ->map(fn (Perumahan $perumahan) => [
                    'id' => $perumahan->id,
                    'nama' => $perumahan->nama,
                    'krama_count' => $perumahan->krama_count,
                    'latitude' => (float) $perumahan->latitude,
                    'longitude' => (float) $perumahan->longitude,
                ]),
        ]);
    }
}
