<?php

namespace App\Services;

use App\Models\Machine;
use App\Models\Mustahik;
use Carbon\Carbon;

class RfidValidationService
{
    /**
     * Jalankan seluruh validasi business rule sebelum pengambilan beras.
     *
     * Urutan validasi:
     *   A. RFID terdaftar
     *   B. Mustahik aktif
     *   C. Jadwal pengambilan sedang aktif
     *   D. Mesin aktif
     *   E. Stok mesin tersedia
     *   F. Kuota mustahik masih ada
     *
     * @return array{
     *   valid: bool,
     *   message: string,
     *   mustahik: Mustahik|null,
     *   machine: Machine|null,
     *   sisa_kuota_gram: int,
     *   sisa_kuota_kg: float,
     *   allowed_options: array<int>,
     * }
     */
    public function validate(string $rfidUid, Machine $machine): array
    {
        $fail = fn (string $msg) => [
            'valid'             => false,
            'message'           => $msg,
            'mustahik'          => null,
            'machine'           => null,
            'sisa_kuota_gram'   => 0,
            'sisa_kuota_kg'     => 0,
            'allowed_options'   => [],
        ];

        // ── A. RFID terdaftar ─────────────────────────────────────────────
        $mustahik = Mustahik::where('rfid_uid', $rfidUid)->first();

        if (! $mustahik) {
            return $fail('RFID tidak terdaftar dalam sistem');
        }

        // ── B. Mustahik aktif ─────────────────────────────────────────────
        if ($mustahik->status !== 'aktif') {
            return $fail('Mustahik tidak aktif, hubungi petugas Lazismu');
        }

        // ── C. Jadwal pengambilan sedang aktif ────────────────────────────
        if (! $machine->jadwal_mulai || ! $machine->jadwal_selesai) {
            return $fail('Jadwal pengambilan belum diatur');
        }

        $now = Carbon::now();

        if ($now->lt($machine->jadwal_mulai)) {
            return $fail('Jadwal pengambilan belum dibuka. Mulai: ' .
                $machine->jadwal_mulai->format('d M Y H:i'));
        }

        if ($now->gt($machine->jadwal_selesai)) {
            return $fail('Jadwal pengambilan sudah ditutup');
        }

        // ── D. Mesin aktif ────────────────────────────────────────────────
        if ($machine->status_mesin !== 'aktif') {
            $info = match ($machine->status_mesin) {
                'maintenance' => 'sedang dalam perbaikan',
                default       => 'tidak aktif',
            };
            return $fail("Mesin {$info}, hubungi petugas");
        }

        // ── E. Stok mesin tersedia ────────────────────────────────────────
        if ($machine->stok_beras_kg <= 0) {
            return $fail('Stok beras pada mesin ini habis');
        }

        // ── F. Kuota mustahik masih ada ───────────────────────────────────
        $sisaKuotaGram = $mustahik->jatah_beras_gram;

        if ($sisaKuotaGram <= 0) {
            return $fail('Kuota pengambilan sudah habis untuk periode ini');
        }

        // Hitung pilihan yang tersedia (dalam kg, dari 1 kg hingga sisa kuota)
        $sisaKuotaKg       = (int) floor($sisaKuotaGram / 1000);
        $maxAmbilKg        = min($sisaKuotaKg, $machine->stok_beras_kg);
        $allowedOptions    = range(1, max(1, $maxAmbilKg));

        return [
            'valid'           => true,
            'message'         => 'Validasi berhasil',
            'mustahik'        => $mustahik,
            'machine'         => $machine,
            'sisa_kuota_gram' => $sisaKuotaGram,
            'sisa_kuota_kg'   => $sisaKuotaKg,
            'allowed_options' => $allowedOptions,
        ];
    }
}
