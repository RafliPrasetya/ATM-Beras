<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * GET /api/system/heartbeat
     *
     * Health check endpoint untuk Raspberry Pi.
     * Raspberry Pi bisa ping endpoint ini secara berkala untuk memastikan
     * koneksi ke server masih aktif sebelum mulai melayani transaksi.
     *
     * Tidak memerlukan middleware auth — Raspberry Pi perlu tahu server hidup
     * sebelum mengirim token.
     */
    public function heartbeat(Request $request)
    {
        /** @var \App\Models\Machine|null $machine */
        $machine = $request->attributes->get('api_machine');

        return ApiResponse::success([
            'server_time'    => now()->toIso8601String(),
            'server_time_id' => now()->timezone('Asia/Jakarta')->toIso8601String(),
            'status'         => 'online',
            'machine_id'     => $machine?->id,
            'machine_code'   => $machine?->machine_code,
            'version'        => '1.0.0',
        ], 'Server aktif');
    }
}
