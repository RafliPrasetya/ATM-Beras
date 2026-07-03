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
}
