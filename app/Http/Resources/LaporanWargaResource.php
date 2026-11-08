<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanWargaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nik_tersamar' => $this->nikTersamar(),
            'asal' => $this->asal,
            'jenis' => $this->jenis->value,
            'jenis_label' => $this->jenis->label(),
            'nama_usaha' => $this->nama_usaha,
            'jenis_usaha' => $this->jenis_usaha,
            'alamat' => $this->alamat,
            'tanggal_mulai' => $this->tanggal_mulai->toDateString(),
            'tanggal_selesai' => $this->tanggal_selesai?->toDateString(),
            'menetap' => $this->menetap,
            'status' => $this->status->value,
            'ditindak_oleh' => $this->penindak?->name,
            'ditindak_at' => $this->ditindak_at?->toDateTimeString(),
            'dibuat_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
