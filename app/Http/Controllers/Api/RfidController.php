<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ValidateRfidRequest;
use App\Http\Resources\Api\MustahikValidationResource;
use App\Services\ApiLogService;
use App\Services\RfidValidationService;
use Throwable;

class RfidController extends Controller
{
    public function __construct(
        private readonly RfidValidationService $validationService,
        private readonly ApiLogService $logService,
    ) {}

    /**
     * POST /api/rfid/validate
     *
     * Validasi kartu RFID yang ditempelkan ke reader Raspberry Pi.
     * Menjalankan 6 layer business rule check secara berurutan.
     */
    public function validate(ValidateRfidRequest $request)
    {
        /** @var \App\Models\Machine $machine */
        $machine  = $request->attributes->get('api_machine');
        $rfidUid  = $request->rfid_uid;
        $endpoint = 'POST /api/rfid/validate';
        $ip       = $request->ip();
        $payload  = ['rfid_uid' => $rfidUid];

        try {
            $result = $this->validationService->validate($rfidUid, $machine);

            if (! $result['valid']) {
                $this->logService->failed($machine, $endpoint, $rfidUid, $result['message'], $ip, $payload);

                return ApiResponse::error($result['message'], null, 422);
            }

            $this->logService->success($machine, $endpoint, $rfidUid, $result['message'], $ip, $payload);

            return ApiResponse::success(
                (new MustahikValidationResource($result))->resolve(),
                'Validasi berhasil'
            );

        } catch (Throwable $e) {
            $this->logService->error($machine, $endpoint, $rfidUid, $e->getMessage(), $ip, $payload);

            return ApiResponse::serverError('Terjadi kesalahan sistem, coba lagi');
        }
    }
}
