<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perumahan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PerumahanController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Perumahan::withCount('krama')->orderBy('nama')->get(['id', 'nama', 'latitude', 'longitude']));
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(Perumahan::create($this->validasi($request)), 201);
    }

    public function update(Request $request, Perumahan $perumahan): JsonResponse
    {
        $perumahan->update($this->validasi($request, $perumahan));

        return response()->json($perumahan);
    }

    public function destroy(Perumahan $perumahan): JsonResponse
    {
        if ($perumahan->krama()->withTrashed()->exists()) {
            throw ValidationException::withMessages([
                'perumahan' => 'Perumahan masih dipakai data krama, jadi tidak bisa dihapus.',
            ]);
        }

        $perumahan->delete();

        return response()->json(status: 204);
    }

    private function validasi(Request $request, ?Perumahan $perumahan = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100', Rule::unique('perumahan', 'nama')->ignore($perumahan)],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
        ], [
            'nama.required' => 'Nama perumahan wajib diisi.',
            'nama.unique' => 'Nama perumahan ini sudah ada.',
            'latitude.required_with' => 'Latitude wajib diisi bersama longitude.',
            'latitude.between' => 'Latitude harus di antara -90 dan 90.',
            'longitude.required_with' => 'Longitude wajib diisi bersama latitude.',
            'longitude.between' => 'Longitude harus di antara -180 dan 180.',
        ]);
    }
}
