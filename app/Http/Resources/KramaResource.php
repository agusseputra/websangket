<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KramaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'nik_tersamar' => $this->nikTersamar(),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_data' => $this->status_data->value,
            'alasan_pending' => $this->alasanPending(),
            'asal' => $this->asal,
            'pekerjaan' => $this->pekerjaan,
            'perumahan_id' => $this->perumahan_id,
            'perumahan' => $this->perumahan?->nama,
            'nomor_rumah' => $this->nomor_rumah,
            'jumlah_penghuni' => $this->jumlah_penghuni,
            'tanggal_mulai_menetap' => $this->tanggal_mulai_menetap?->toDateString(),
            'ada_lokasi' => $this->latitude !== null,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'lokasi_lengkap' => $this->lokasiLengkap(),
            'no_hp' => $this->no_hp,
            'keterangan' => $this->keterangan,
            'aktif' => $this->aktif,
        ];
    }
}
