<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImporPembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImporPembayaranController extends Controller
{
    private const FOLDER = 'impor-pembayaran';

    public function __construct(private ImporPembayaranService $impor) {}

    public function status(): JsonResponse
    {
        return response()->json(['sudah_diimpor' => $this->impor->sudahDiimpor()]);
    }

    public function pratinjau(Request $request): JsonResponse
    {
        $this->pastikanBelumDiimpor();

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes' => 'File harus berformat .xlsx.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        Storage::disk('local')->deleteDirectory(self::FOLDER);

        $token = (string) Str::uuid();
        $request->file('file')->storeAs(self::FOLDER, "{$token}.xlsx", 'local');

        try {
            return response()->json(['token' => $token, ...$this->impor->pratinjau($this->pathFile($token))]);
        } catch (ValidationException $error) {
            Storage::disk('local')->deleteDirectory(self::FOLDER);

            throw $error;
        }
    }

    public function simpan(Request $request): JsonResponse
    {
        $this->pastikanBelumDiimpor();

        $token = $request->validate(['token' => ['required', 'uuid']])['token'];

        if (! Storage::disk('local')->exists(self::FOLDER."/{$token}.xlsx")) {
            throw ValidationException::withMessages(['token' => 'Pratinjau sudah tidak berlaku. Unggah ulang filenya.']);
        }

        $hasil = $this->impor->impor($this->pathFile($token), $request->user());
        Storage::disk('local')->deleteDirectory(self::FOLDER);

        return response()->json($hasil, 201);
    }

    public function batal(): JsonResponse
    {
        Storage::disk('local')->deleteDirectory(self::FOLDER);

        return response()->json(status: 204);
    }

    private function pathFile(string $token): string
    {
        return Storage::disk('local')->path(self::FOLDER."/{$token}.xlsx");
    }

    private function pastikanBelumDiimpor(): void
    {
        if ($this->impor->sudahDiimpor()) {
            throw ValidationException::withMessages(['file' => 'Periode '.ImporPembayaranService::TAHUN.' sudah ada, jadi pembayaran Excel tidak bisa diimpor lagi.']);
        }
    }
}
