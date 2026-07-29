<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineToken;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProvisionController extends Controller
{
    /**
     * POST /api/machine/provision
     *
     * Self-provisioning endpoint untuk Raspberry Pi.
     * Pi mengirimkan machine_code → server generate token baru → return token + machine_id.
     *
     * Endpoint ini PUBLIC (tanpa auth) tapi dilindungi rate limiter ketat (throttle:5,1).
     */
    public function provision(Request $request)
    {
        $request->validate([
            'machine_code' => 'required|string',
        ], [
            'machine_code.required' => 'Kode mesin (machine_code) wajib diisi.',
        ]);

        $machineCode = $request->input('machine_code');

        // Cari mesin berdasarkan machine_code
        $machine = Machine::where('machine_code', $machineCode)->first();

        if (! $machine) {
            return ApiResponse::error(
                "Mesin dengan kode '{$machineCode}' tidak ditemukan di database. Pastikan mesin sudah didaftarkan oleh admin via Web Admin.",
                null,
                404
            );
        }

        // Revoke semua token lama untuk mesin ini (1 mesin = 1 token aktif)
        $machine->tokens()->delete();

        // Generate token baru (64 karakter random)
        $rawToken = Str::random(64);

        MachineToken::create([
            'machine_id' => $machine->id,
            'token'      => $rawToken,
            'name'       => "Auto-Provision-" . now()->format('YmdHis'),
        ]);

        return ApiResponse::success([
            'machine_id'   => $machine->id,
            'machine_code' => $machine->machine_code,
            'token'        => $rawToken,
        ], 'Provisioning berhasil. Token telah di-generate untuk mesin ini.');
    }
}
