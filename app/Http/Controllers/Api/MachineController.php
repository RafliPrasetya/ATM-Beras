<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MachineStatusResource;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * GET /api/machine/status
     *
     * Mengembalikan status lengkap mesin: stok, jadwal, kesiapan operasi.
     * Raspberry Pi bisa polling endpoint ini saat startup atau setiap beberapa menit.
     */
    public function status(Request $request)
    {
        /** @var \App\Models\Machine $machine */
        $machine = $request->attributes->get('api_machine');

        // Refresh dari DB agar data terbaru
        $machine->refresh();

        return ApiResponse::success(
            (new MachineStatusResource($machine))->resolve(),
            'Status mesin berhasil diambil'
        );
    }

    /**
     * POST /api/machine/update-stock
     *
     * Update stok beras mesin dari hardware (sensor loadcell)
     * baik saat terjadi transaksi maupun saat pengisian beras ulang.
     */
    public function updateStock(Request $request)
    {
        /** @var \App\Models\Machine $machine */
        $machine = $request->attributes->get('api_machine');

        $request->validate([
            'stok_beras_kg' => 'required|numeric|min:0',
        ], [
            'stok_beras_kg.required' => 'Stok beras wajib diisi.',
            'stok_beras_kg.numeric'  => 'Stok beras harus berupa angka.',
            'stok_beras_kg.min'      => 'Stok beras tidak boleh negatif.',
        ]);

        $machine->update([
            'stok_beras_kg' => (int) round($request->stok_beras_kg),
        ]);

        return ApiResponse::success(
            (new MachineStatusResource($machine->fresh()))->resolve(),
            'Stok beras mesin berhasil diperbarui via sensor loadcell API'
        );
    }
}
