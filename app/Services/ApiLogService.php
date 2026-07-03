<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\Machine;

class ApiLogService
{
    /**
     * Catat request API dari Raspberry Pi ke database.
     *
     * @param  array  $payload  Body request mentah
     */
    public function log(
        ?Machine $machine,
        string $endpoint,
        ?string $rfidUid,
        string $status,        // 'success' | 'failed' | 'error'
        string $message,
        string $ipAddress,
        array $payload = []
    ): ApiLog {
        return ApiLog::create([
            'machine_id' => $machine?->id,
            'rfid_uid'   => $rfidUid,
            'endpoint'   => $endpoint,
            'ip_address' => $ipAddress,
            'status'     => $status,
            'message'    => $message,
            'payload'    => $payload,
        ]);
    }

    /**
     * Shortcut untuk log sukses.
     */
    public function success(
        ?Machine $machine,
        string $endpoint,
        ?string $rfidUid,
        string $message,
        string $ipAddress,
        array $payload = []
    ): ApiLog {
        return $this->log($machine, $endpoint, $rfidUid, 'success', $message, $ipAddress, $payload);
    }

    /**
     * Shortcut untuk log gagal (business rule violation).
     */
    public function failed(
        ?Machine $machine,
        string $endpoint,
        ?string $rfidUid,
        string $message,
        string $ipAddress,
        array $payload = []
    ): ApiLog {
        return $this->log($machine, $endpoint, $rfidUid, 'failed', $message, $ipAddress, $payload);
    }

    /**
     * Shortcut untuk log error (exception / server error).
     */
    public function error(
        ?Machine $machine,
        string $endpoint,
        ?string $rfidUid,
        string $message,
        string $ipAddress,
        array $payload = []
    ): ApiLog {
        return $this->log($machine, $endpoint, $rfidUid, 'error', $message, $ipAddress, $payload);
    }
}
