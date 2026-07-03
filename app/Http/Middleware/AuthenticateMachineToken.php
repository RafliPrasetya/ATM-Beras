<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Models\MachineToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMachineToken
{
    /**
     * Validasi Bearer Token dari Raspberry Pi.
     *
     * Header yang diperlukan:
     *   Authorization: Bearer <token>
     *
     * Jika valid, inject machine ke dalam request sebagai 'api_machine'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (! $bearerToken) {
            return ApiResponse::unauthorized('Token autentikasi diperlukan');
        }

        // Cari token di database
        $machineToken = MachineToken::with('machine')
            ->where('token', $bearerToken)
            ->first();

        if (! $machineToken) {
            return ApiResponse::unauthorized('Token tidak valid atau tidak ditemukan');
        }

        if (! $machineToken->machine) {
            return ApiResponse::unauthorized('Mesin tidak ditemukan');
        }

        // Tandai token digunakan (update last_used_at)
        $machineToken->markAsUsed();

        // Inject machine ke request agar bisa diakses di controller
        $request->merge(['api_machine' => $machineToken->machine]);
        $request->attributes->set('api_machine', $machineToken->machine);

        return $next($request);
    }
}
