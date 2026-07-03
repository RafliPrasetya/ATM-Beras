<?php

namespace App\Console\Commands;

use App\Models\Machine;
use App\Models\MachineToken;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateMachineToken extends Command
{
    protected $signature = 'machine:token
                            {machine_code : Kode mesin (machine_code)}
                            {--name= : Label untuk token (misal: RPi-Unit-01)}
                            {--revoke : Hapus semua token lama sebelum generate baru}';

    protected $description = 'Generate API Bearer Token untuk Raspberry Pi berdasarkan kode mesin';

    public function handle(): int
    {
        $machineCode = $this->argument('machine_code');
        $label       = $this->option('name') ?? "Token-{$machineCode}-" . now()->format('YmdHis');
        $revoke      = $this->option('revoke');

        // Cari mesin
        $machine = Machine::where('machine_code', $machineCode)->first();

        if (! $machine) {
            $this->error("Mesin dengan kode '{$machineCode}' tidak ditemukan.");
            $this->line('Gunakan perintah berikut untuk melihat daftar mesin:');
            $this->line('  php artisan tinker --execute="App\Models\Machine::pluck(\'machine_code\')"');

            return Command::FAILURE;
        }

        // Revoke token lama jika diminta
        if ($revoke) {
            $count = $machine->tokens()->count();
            $machine->tokens()->delete();
            $this->warn("🗑  {$count} token lama dihapus.");
        }

        // Generate token baru (64 random bytes → 128 hex chars)
        $rawToken = Str::random(64);

        MachineToken::create([
            'machine_id' => $machine->id,
            'token'      => $rawToken,
            'name'       => $label,
        ]);

        $this->newLine();
        $this->line('✅ <fg=green>Token berhasil di-generate!</>');
        $this->newLine();

        $this->table(
            ['Kunci', 'Nilai'],
            [
                ['Mesin',       $machine->machine_code],
                ['ID Mesin',    $machine->id],
                ['Label Token', $label],
                ['Token',       $rawToken],
            ]
        );

        $this->newLine();
        $this->info('Salin token di atas dan simpan di file konfigurasi Raspberry Pi.');
        $this->warn('⚠  Token hanya ditampilkan sekali. Simpan sekarang!');
        $this->newLine();

        $this->line('Contoh penggunaan di Raspberry Pi (Python):');
        $this->line("  headers = {'Authorization': 'Bearer {$rawToken}'}");
        $this->newLine();

        return Command::SUCCESS;
    }
}
