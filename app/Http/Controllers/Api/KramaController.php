<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusKrama;
use App\Http\Controllers\Controller;
use App\Http\Requests\KramaRequest;
use App\Http\Resources\KramaResource;
use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Services\GenerateTagihanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class KramaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::enum(StatusKrama::class)],
            'status_data' => ['nullable', 'in:lengkap,pending'],
            'perumahan_id' => ['nullable', 'integer'],
            'lokasi' => ['nullable', 'in:ada,belum'],
        ]);

        $krama = Krama::with('perumahan')
            ->when($filter['q'] ?? null, function ($query, string $q) {
                $q = trim($q);

                // NIK terenkripsi hanya bisa dicari dengan nilai lengkap lewat hash.
                preg_match('/^\d{16}$/', $q)
                    ? $query->where('nik_hash', Krama::hashNik($q))
                    : $query->where('nama', 'like', '%'.addcslashes($q, '%_\\').'%');
            })
            ->when($filter['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filter['status_data'] ?? null, fn ($query, $status) => $query->where('status_data', $status))
            ->when($filter['perumahan_id'] ?? null, fn ($query, $id) => $query->where('perumahan_id', $id))
            ->when(($filter['lokasi'] ?? null) === 'ada', fn ($query) => $query->whereNotNull('latitude'))
            ->when(($filter['lokasi'] ?? null) === 'belum', fn ($query) => $query->whereNull('latitude'))
            ->orderBy('nama')
            ->paginate(config('sangket.per_halaman'))
            ->withQueryString();

        return KramaResource::collection($krama);
    }

    public function store(KramaRequest $request, GenerateTagihanService $generate): JsonResponse
    {
        $krama = DB::transaction(function () use ($request, $generate) {
            $krama = Krama::create($request->dataKrama());
            $periode = PeriodeIuran::aktif();

            // Krama baru langsung punya tagihan agar bisa dibayar tanpa menunggu Generate.
            if ($periode && $krama->aktif) {
                $generate->untukKrama($krama, $periode);
            }

            return $krama;
        });

        return KramaResource::make($krama->load('perumahan'))->response()->setStatusCode(201);
    }

    public function show(Krama $krama): KramaResource
    {
        return KramaResource::make($krama->load('perumahan'));
    }

    public function update(KramaRequest $request, Krama $krama): KramaResource
    {
        $krama->update($request->dataKrama());

        return KramaResource::make($krama->load('perumahan'));
    }

    public function lokasi(Request $request, Krama $krama): KramaResource
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'perumahan_id' => ['required', 'integer', 'exists:perumahan,id'],
            'nomor_rumah' => ['nullable', 'string', 'max:30'],
        ], [
            'latitude.required' => 'Tandai titik lokasi terlebih dahulu.',
            'longitude.required' => 'Tandai titik lokasi terlebih dahulu.',
            'perumahan_id.required' => 'Perumahan wajib dipilih.',
        ]);

        $krama->update([
            ...$data,
            'lokasi_ditandai_oleh' => $request->user()->id,
            'lokasi_ditandai_at' => now(),
        ]);

        return KramaResource::make($krama->load('perumahan'));
    }

    public function destroy(Krama $krama): JsonResponse
    {
        $krama->delete();

        return response()->json(status: 204);
    }

    public function nik(Request $request, Krama $krama): JsonResponse
    {
        Log::info('NIK krama dibuka', ['krama_id' => $krama->id, 'user_id' => $request->user()->id]);

        return response()->json(['nik' => $krama->nik]);
    }
}
