<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Format response untuk status mesin (GET /api/machine/status).
 */
class MachineStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var \App\Models\Machine $machine */
        $machine = $this->resource;

        return [
            'id'           => $machine->id,
            'kode'         => $machine->machine_code,
            'lokasi'       => $machine->lokasi_penempatan,
            'status_mesin' => $machine->status_mesin,
            'stok_beras_kg' => $machine->stok_beras_kg,
            'jadwal' => [
                'status'   => $machine->status_penjadwalan,
                'mulai'    => $machine->jadwal_mulai?->toIso8601String(),
                'selesai'  => $machine->jadwal_selesai?->toIso8601String(),
            ],
            'siap_beroperasi' => $machine->status_mesin === 'aktif'
                && $machine->status_penjadwalan === 'aktif'
                && $machine->stok_beras_kg > 0,
        ];
    }
}
