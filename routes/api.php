<?php

use App\Http\Controllers\Api\MachineController;
use App\Http\Controllers\Api\RfidController;
use App\Http\Controllers\Api\SystemController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — ATM Beras (Raspberry Pi)
|--------------------------------------------------------------------------
|
| Semua route di sini dikonsumsi oleh perangkat Raspberry Pi.
|
| Autentikasi: Bearer Token per mesin (tabel machine_tokens).
| Middleware 'auth.machine' memvalidasi token dan meng-inject machine
| ke dalam request sebagai request attribute 'api_machine'.
|
*/

// ── Public (tanpa auth) ──────────────────────────────────────────────────
Route::get('/system/heartbeat', [SystemController::class, 'heartbeat'])
    ->name('api.system.heartbeat');

// ── Protected (wajib Bearer Token mesin) ────────────────────────────────
Route::middleware(['auth.machine', 'throttle:60,1'])->group(function () {

    // Heartbeat (versi auth — mengembalikan info mesin)
    Route::get('/system/heartbeat/auth', [SystemController::class, 'heartbeat'])
        ->name('api.system.heartbeat.auth');

    // Status mesin
    Route::get('/machine/status', [MachineController::class, 'status'])
        ->name('api.machine.status');

    // Update stok beras mesin (sensor loadcell / pengisian beras)
    Route::post('/machine/update-stock', [MachineController::class, 'updateStock'])
        ->name('api.machine.update-stock');

    // Validasi RFID
    Route::post('/rfid/validate', [RfidController::class, 'validate'])
        ->name('api.rfid.validate');

    // Proses transaksi pengambilan beras
    Route::post('/transaction/process', [TransactionController::class, 'process'])
        ->name('api.transaction.process');
});
