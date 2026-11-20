<?php

namespace App\Http\Controllers\Api;

use App\Enums\JenisLaporan;
use App\Enums\StatusLaporan;
use App\Http\Controllers\Controller;
use App\Http\Resources\LaporanWargaResource;
use App\Models\LaporanWarga;
use App\Services\CaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LaporanWargaController extends Controller
{
    /**
     * Laporan warga dari landing page, tanpa login.
     */
    public function store(Request $request, CaptchaService $captcha): JsonResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nik' => ['required', 'digits:16'],
            'asal' => ['required', 'string', 'max:150'],
            'jenis' => ['required', Rule::enum(JenisLaporan::class)],
            'nama_usaha' => ['exclude_unless:jenis,usaha', 'required', 'string', 'max:150'],
            'jenis_usaha' => ['exclude_unless:jenis,usaha', 'required', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'menetap' => ['required', 'boolean'],
            'tanggal_selesai' => ['exclude_if:menetap,true', 'required', 'date', 'after_or_equal:tanggal_mulai'],
            'captcha_kunci' => ['required', 'uuid'],
            'captcha' => ['required', 'string', 'max:10'],
        ], [
            'nik.digits' => 'NIK harus 16 digit angka.',
            'tanggal_selesai.required' => 'Isi tanggal selesai, atau pilih menetap.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'captcha_kunci.required' => 'Muat ulang kode CAPTCHA.',
            'captcha_kunci.uuid' => 'Muat ulang kode CAPTCHA.',
            'captcha.required' => 'Ketik kode CAPTCHA pada gambar.',
        ]);

        // CAPTCHA diperiksa setelah isian lain lolos, agar kodenya tidak hangus hanya karena isian lain salah.
        if (! $captcha->periksa($data['captcha_kunci'], $data['captcha'])) {
            throw ValidationException::withMessages(['captcha' => 'Kode CAPTCHA salah atau kedaluwarsa. Ketik kode pada gambar yang baru.']);
        }

        unset($data['captcha'], $data['captcha_kunci']);

        LaporanWarga::create($data);

        return response()->json(['message' => 'Laporan diterima. Terima kasih atas partisipasi Anda.'], 201);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::enum(StatusLaporan::class)],
            'jenis' => ['nullable', Rule::enum(JenisLaporan::class)],
        ]);

        $laporan = LaporanWarga::with('penindak')
            ->when($filter['q'] ?? null, fn ($query, $q) => $query->where('nama', 'like', '%'.addcslashes($q, '%_\\').'%'))
            ->when($filter['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filter['jenis'] ?? null, fn ($query, $jenis) => $query->where('jenis', $jenis))
            ->latest('id')
            ->paginate(config('sangket.per_halaman'))
            ->withQueryString();

        return LaporanWargaResource::collection($laporan);
    }

    public function ubahStatus(Request $request, LaporanWarga $laporan): LaporanWargaResource
    {
        $status = StatusLaporan::from($request->validate([
            'status' => ['required', Rule::enum(StatusLaporan::class)],
        ])['status']);

        $ditindak = $status === StatusLaporan::Ditindak;

        $laporan->update([
            'status' => $status,
            'ditindak_oleh' => $ditindak ? $request->user()->id : null,
            'ditindak_at' => $ditindak ? now() : null,
        ]);

        return LaporanWargaResource::make($laporan->load('penindak'));
    }

    public function nik(Request $request, LaporanWarga $laporan): JsonResponse
    {
        Log::info('NIK laporan warga dibuka', ['laporan_id' => $laporan->id, 'user_id' => $request->user()->id]);

        return response()->json(['nik' => $laporan->nik]);
    }
}
