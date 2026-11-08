<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Enums\StatusKrama;
use App\Models\Krama;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kramaId = $this->route('krama')?->id;

        return [
            'nik' => [
                'sometimes', 'nullable', 'digits:16',
                function (string $attribute, mixed $value, Closure $fail) use ($kramaId) {
                    $terpakai = Krama::withTrashed()
                        ->where('nik_hash', Krama::hashNik((string) $value))
                        ->when($kramaId, fn ($query) => $query->whereKeyNot($kramaId))
                        ->exists();

                    if ($terpakai) {
                        $fail('NIK ini sudah terdaftar.');
                    }
                },
            ],
            'nama' => ['required', 'string', 'max:150'],
            'status' => ['sometimes', Rule::enum(StatusKrama::class)],
            'asal' => ['nullable', 'string', 'max:150'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            // Krama baru wajib punya perumahan. Data impor lama boleh dilengkapi bertahap.
            'perumahan_id' => [$kramaId ? 'nullable' : 'required', 'integer', 'exists:perumahan,id'],
            'nomor_rumah' => ['nullable', 'string', 'max:30'],
            'jumlah_penghuni' => ['nullable', 'integer', 'min:0', 'max:999'],
            'tanggal_mulai_menetap' => ['nullable', 'date', 'before_or_equal:today'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'aktif' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus 16 digit angka.',
            'nama.required' => 'Nama wajib diisi.',
            'perumahan_id.required' => 'Perumahan wajib dipilih.',
            'perumahan_id.exists' => 'Perumahan tidak ditemukan.',
            'tanggal_mulai_menetap.before_or_equal' => 'Tanggal mulai menetap tidak boleh di masa depan.',
        ];
    }

    /**
     * Status krama dan keaktifan hanya bisa diubah admin.
     */
    public function dataKrama(): array
    {
        $data = $this->validated();

        return $this->user()->role === Role::Admin ? $data : collect($data)->except(['status', 'aktif'])->all();
    }
}
