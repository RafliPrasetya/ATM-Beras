<?php

namespace App\Services;

use App\Models\Machine;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionService
{
    public function __construct(
        private readonly RfidValidationService $validationService
    ) {}

    /**
     * Proses transaksi pengambilan beras secara atomik.
     *
     * Re-validasi SEMUA business rule sebelum menulis ke database.
     * Tidak boleh mempercayai data yang dikirim Raspberry Pi begitu saja.
     *
     * @param  int  $jumlahAmbilKg  Jumlah yang dipilih pengguna (satuan kg)
     *
     * @return array{
     *   success: bool,
     *   message: string,
     *   transaction: Transaction|null,
     *   sisa_kuota_gram: int,
     *   sisa_kuota_kg: float,
     *   stok_mesin_kg: int,
     * }
     *
     * @throws Throwable
     */
    public function process(string $rfidUid, Machine $machine, int $jumlahAmbilKg): array
    {
        $fail = fn (string $msg) => [
            'success'         => false,
            'message'         => $msg,
            'transaction'     => null,
            'sisa_kuota_gram' => 0,
            'sisa_kuota_kg'   => 0,
            'stok_mesin_kg'   => $machine->fresh()->stok_beras_kg,
        ];

        // ── RE-VALIDASI (tidak percaya data Pi) ───────────────────────────
        $validation = $this->validationService->validate($rfidUid, $machine->fresh());

        if (! $validation['valid']) {
            return $fail($validation['message']);
        }

        $mustahik      = $validation['mustahik'];
        $sisaKuotaGram = $validation['sisa_kuota_gram'];
        $jumlahAmbilGram = $jumlahAmbilKg * 1000;

        // Pastikan jumlah yang diminta tidak melebihi sisa kuota
        if ($jumlahAmbilGram > $sisaKuotaGram) {
            return $fail('Jumlah pengambilan melebihi sisa kuota Anda');
        }

        // Pastikan jumlah yang diminta tidak melebihi stok mesin
        if ($jumlahAmbilKg > $machine->stok_beras_kg) {
            return $fail('Jumlah pengambilan melebihi stok mesin');
        }

        // ── DB TRANSACTION (atomik) ───────────────────────────────────────
        $transaction = DB::transaction(function () use (
            $machine,
            $mustahik,
            $jumlahAmbilGram,
            $jumlahAmbilKg
        ): Transaction {
            // 1. Kurangi stok mesin
            $machine->decrement('stok_beras_kg', $jumlahAmbilKg);

            // 2. Kurangi jatah/kuota beras mustahik
            $mustahik->decrement('jatah_beras_gram', $jumlahAmbilGram);

            // 3. Catat transaksi
            $transaction = Transaction::create([
                'mustahik_id'         => $mustahik->id,
                'machine_id'          => $machine->id,
                'jumlah_ambil_gram'   => $jumlahAmbilGram,
                'tanggal_pengambilan' => Carbon::now(),
            ]);

            return $transaction;
        });

        // Ambil data terbaru setelah transaksi berhasil
        $mustahikSegar = $mustahik->fresh();
        $sisaSetelahGram = $mustahikSegar->jatah_beras_gram;
        $mesinSegar = $machine->fresh();

        return [
            'success'         => true,
            'message'         => 'Pengambilan beras berhasil',
            'transaction'     => $transaction,
            'sisa_kuota_gram' => max(0, $sisaSetelahGram),
            'sisa_kuota_kg'   => max(0, (int) floor($sisaSetelahGram / 1000)),
            'stok_mesin_kg'   => $mesinSegar->stok_beras_kg,
        ];
    }
}
