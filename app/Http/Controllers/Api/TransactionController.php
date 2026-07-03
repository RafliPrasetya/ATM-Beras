<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProcessTransactionRequest;
use App\Http\Resources\Api\TransactionResultResource;
use App\Models\Machine;
use App\Services\ApiLogService;
use App\Services\TransactionService;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
        private readonly ApiLogService $logService,
    ) {}

    /**
     * POST /api/transaction/process
     *
     * Proses pengambilan beras setelah mustahik memilih jumlah.
     * Seluruh business rule divalidasi ulang di sisi server — tidak mempercayai
     * data dari Raspberry Pi.
     */
    public function process(ProcessTransactionRequest $request)
    {
        /** @var \App\Models\Machine $authenticatedMachine */
        $authenticatedMachine = $request->attributes->get('api_machine');
        $rfidUid   = $request->rfid_uid;
        $machineId = $request->machine_id;
        $jumlahKg  = (int) $request->jumlah_ambil;
        $endpoint  = 'POST /api/transaction/process';
        $ip        = $request->ip();
        $payload   = $request->only(['rfid_uid', 'machine_id', 'jumlah_ambil']);

        // Pastikan machine_id di body cocok dengan token yang digunakan
        // (mencegah Pi mesin A memproses transaksi atas nama mesin B)
        if ($authenticatedMachine->id !== (int) $machineId) {
            $this->logService->failed($authenticatedMachine, $endpoint, $rfidUid,
                'Machine ID tidak sesuai token', $ip, $payload);

            return ApiResponse::forbidden('Machine ID tidak sesuai dengan token autentikasi');
        }

        $machine = Machine::find($machineId);

        if (! $machine) {
            return ApiResponse::error('Mesin tidak ditemukan', null, 404);
        }

        try {
            $result = $this->transactionService->process($rfidUid, $machine, $jumlahKg);

            if (! $result['success']) {
                $this->logService->failed($machine, $endpoint, $rfidUid, $result['message'], $ip, $payload);

                return ApiResponse::error($result['message'], null, 422);
            }

            $this->logService->success($machine, $endpoint, $rfidUid, $result['message'], $ip, $payload);

            return ApiResponse::success(
                (new TransactionResultResource($result))->resolve(),
                'Pengambilan berhasil'
            );

        } catch (Throwable $e) {
            $this->logService->error($machine, $endpoint, $rfidUid, $e->getMessage(), $ip, $payload);

            return ApiResponse::serverError('Gagal memproses transaksi, coba lagi');
        }
    }
}
