<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Krama;
use App\Services\ImporKramaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImporKramaController extends Controller
{
    private const FOLDER = 'impor';

    public function __construct(private ImporKramaService $impor) {}

    public function status(): JsonResponse
    {
        return response()->json(['sudah_diimpor' => $this->sudahDiimpor()]);
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

        // File berisi NIK, jadi hanya satu salinan sementara yang disimpan.
        Storage::disk('local')->deleteDirectory(self::FOLDER);

        $token = (string) Str::uuid();
        $request->file('file')->storeAs(self::FOLDER, "{$token}.xlsx", 'local');

        try {
            $laporan = $this->impor->pratinjau($this->pathFile($token));
        } catch (ValidationException $error) {
            Storage::disk('local')->deleteDirectory(self::FOLDER);

            throw $error;
        }

        return response()->json(['token' => $token, ...$laporan]);
    }

    public function simpan(Request $request): JsonResponse
    {
        $this->pastikanBelumDiimpor();

        $token = $request->validate(['token' => ['required', 'uuid']])['token'];
        $relatif = self::FOLDER."/{$token}.xlsx";

        if (! Storage::disk('local')->exists($relatif)) {
            throw ValidationException::withMessages(['token' => 'Pratinjau sudah tidak berlaku. Unggah ulang filenya.']);
        }

        $hasil = $this->impor->impor($this->pathFile($token));
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

    private function sudahDiimpor(): bool
    {
        return Krama::withTrashed()->whereNotNull('impor_baris')->exists();
    }

    private function pastikanBelumDiimpor(): void
    {
        if ($this->sudahDiimpor()) {
            throw ValidationException::withMessages(['file' => 'Data krama dari Excel sudah pernah diimpor.']);
        }
    }
}
