<?php

namespace Database\Seeders;

use App\Models\Machine;
use App\Models\Mustahik;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $mustahik = Mustahik::first();

        $machine = Machine::first();

        if (!$mustahik || !$machine) {
            return;
        }

        Transaction::create([
            'mustahik_id' => $mustahik->id,
            'machine_id' => $machine->id,
            'jumlah_ambil_gram' => 3000,
            'tanggal_pengambilan' => now()->subDays(14),
        ]);

        Transaction::create([
            'mustahik_id' => $mustahik->id,
            'machine_id' => $machine->id,
            'jumlah_ambil_gram' => 3000,
            'tanggal_pengambilan' => now()->subDays(7),
        ]);

        Transaction::create([
            'mustahik_id' => $mustahik->id,
            'machine_id' => $machine->id,
            'jumlah_ambil_gram' => 3000,
            'tanggal_pengambilan' => now(),
        ]);
    }
}
