<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Format response setelah transaksi berhasil diproses.
 */
class TransactionResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'status'        => true,
            'message'       => $this->resource['message'],
            'transaction_id' => $this->resource['transaction']->id,
            'sisa_kuota' => [
                'gram' => $this->resource['sisa_kuota_gram'],
                'kg'   => $this->resource['sisa_kuota_kg'],
            ],
            'stok_mesin_kg' => $this->resource['stok_mesin_kg'],
        ];
    }
}
