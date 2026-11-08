<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagihanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'krama_id' => $this->krama_id,
            'krama' => $this->whenLoaded('krama', fn () => $this->krama?->nama),
            'tahun' => $this->whenLoaded('periodeIuran', fn () => (int) $this->periodeIuran->tahun),
            'jenis' => $this->jenis->value,
            'jenis_label' => $this->jenis->label(),
            'nominal' => (int) $this->nominal,
            'kekurangan_sebelumnya' => (int) $this->kekurangan_sebelumnya,
            'total_tagihan' => (int) $this->nominal + (int) $this->kekurangan_sebelumnya,
            'nominal_bayar' => $this->nominal_bayar === null ? null : (int) $this->nominal_bayar,
            'sisa' => $this->sisa(),
            'sisa_dipindahkan' => $this->sisa_dipindah_ke_id !== null,
            'tanggal_bayar' => $this->tanggal_bayar?->toDateString(),
            'metode' => $this->metode?->value,
            'status' => $this->status->value,
            'no_struk' => $this->no_struk,
            'petugas' => $this->whenLoaded('petugas', fn () => $this->petugas?->name),
            'keterangan' => $this->keterangan,
        ];
    }
}
