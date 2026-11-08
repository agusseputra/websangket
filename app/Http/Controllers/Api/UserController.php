<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(User::orderBy('name')->get(['id', 'name', 'email', 'role', 'aktif']));
    }

    /**
     * Pilihan filter petugas. Admin ikut karena juga bisa menerima pembayaran, dan hanya nama yang dikirim.
     */
    public function pilihanPetugas(): JsonResponse
    {
        return response()->json(User::orderBy('name')->get(['id', 'name']));
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(User::create($this->validasi($request)), 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $this->validasi($request, $user);

        // Admin tidak boleh mengunci dirinya sendiri dari aplikasi.
        if ($user->is($request->user()) && (($data['aktif'] ?? true) === false || ($data['role'] ?? 'admin') !== 'admin')) {
            throw ValidationException::withMessages(['aktif' => 'Anda tidak bisa menonaktifkan atau menurunkan peran akun sendiri.']);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    private function validasi(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', Password::min(8)],
            'role' => ['required', Rule::enum(Role::class)],
            'aktif' => ['sometimes', 'boolean'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah dipakai.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);
    }
}
