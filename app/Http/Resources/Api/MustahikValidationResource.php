<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Format response setelah validasi RFID berhasil.
 *
 * Resource ini menerima array (bukan Eloquent model)
 * yang dikembalikan oleh RfidValidationService::validate().
 */
class MustahikValidationResource extends JsonResource
{
    /**
     * @param  array{
     *   valid: bool,
     *   mustahik: \App\Models\Mustahik,
     *   machine: \App\Models\Machine,
     *   sisa_kuota_gram: int,
     *   sisa_kuota_kg: float,
     *   allowed_options: array<int>,
     * }  $resource
     */
    public function toArray(Request $request): array
    {
        $mustahik = $this->resource['mustahik'];
        $machine  = $this->resource['machine'];

        return [
            'status'    => true,
            'nama'      => $mustahik->nama,
            'nik'       => $mustahik->nik,
            'sisa_kuota' => [
                'gram' => $this->resource['sisa_kuota_gram'],
                'kg'   => $this->resource['sisa_kuota_kg'],
            ],
            'machine' => [
                'id'      => $machine->id,
                'kode'    => $machine->machine_code,
                'lokasi'  => $machine->lokasi_penempatan,
                'stok_kg' => $machine->stok_beras_kg,
            ],
            'allowed_options' => $this->resource['allowed_options'],
        ];
    }
}
