<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Machine;
use App\Models\Mustahik;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SendScheduleNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'atm:send-whatsapp-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi WA untuk mesin dengan jadwal aktif';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService)
    {
        // Cari mesin yang jadwalnya sedang aktif dan WA belum dikirim
        $machines = Machine::where('is_wa_sent', false)->jadwalAktif()->get();

        if ($machines->isEmpty()) {
            return;
        }

        $mustahiks = Mustahik::whereNotNull('no_hp')
            ->where('no_hp', '!=', '')
            ->where('status', 'aktif')
            ->get();

        foreach ($machines as $machine) {
            $totalBerhasil = 0;
            $totalGagal = 0;

            foreach ($mustahiks as $mustahik) {
                $nomorWhatsApp = $this->formatNomorWhatsApp($mustahik->no_hp);

                if (! $nomorWhatsApp) {
                    $totalGagal++;
                    Log::warning('Notifikasi jadwal WA gagal: nomor tidak valid', [
                        'nama' => $mustahik->nama,
                        'nomor' => $mustahik->no_hp,
                    ]);
                    continue;
                }

                try {
                    $message = $this->buatPesanJadwal($mustahik, $machine);
                    $hasil = $whatsAppService->sendMessage($nomorWhatsApp, $message);

                    if ($hasil['success'] ?? false) {
                        $totalBerhasil++;
                    } else {
                        $totalGagal++;
                        Log::warning('Notifikasi jadwal WA gagal dikirim', [
                            'nama' => $mustahik->nama,
                            'nomor' => $nomorWhatsApp,
                            'pesan_error' => $hasil['response'] ?? 'Gagal',
                        ]);
                    }
                } catch (\Throwable $e) {
                    $totalGagal++;
                    Log::error('Notifikasi jadwal WA error', [
                        'nama' => $mustahik->nama,
                        'nomor' => $nomorWhatsApp,
                        'pesan_error' => $e->getMessage(),
                    ]);
                }
            }

            // Update status mesin agar tidak dikirim ulang
            $machine->update(['is_wa_sent' => true]);

            $this->info("Notifikasi WA untuk mesin {$machine->machine_code} terkirim. Berhasil: {$totalBerhasil}, Gagal: {$totalGagal}");
            
            \Illuminate\Support\Facades\Cache::put('wa_notification_result', [
                'status' => $totalGagal == 0 ? 'success' : 'warning',
                'message' => "Notifikasi WA untuk mesin {$machine->machine_code} terkirim otomatis. Berhasil: {$totalBerhasil}, Gagal: {$totalGagal}"
            ], 600);
        }
    }

    private function formatNomorWhatsApp(?string $nomor): ?string
    {
        if (! $nomor) return null;

        $nomor = preg_replace('/[^0-9]/', '', $nomor);

        if (str_starts_with($nomor, '0')) {
            $nomor = '62'.substr($nomor, 1);
        }

        if (str_starts_with($nomor, '8')) {
            $nomor = '62'.$nomor;
        }

        if (! str_starts_with($nomor, '62')) {
            return null;
        }

        return $nomor;
    }

    private function buatPesanJadwal(Mustahik $mustahik, Machine $machine): string
    {
        $jadwalMulai = $machine->jadwal_mulai;
        $jadwalSelesai = $machine->jadwal_selesai;

        $tanggal = $jadwalMulai->isSameDay($jadwalSelesai)
            ? $jadwalMulai->format('d/m/Y')
            : $jadwalMulai->format('d/m/Y').' - '.$jadwalSelesai->format('d/m/Y');

        $jamMulai = $jadwalMulai->format('H:i');
        $jamSelesai = $jadwalSelesai->format('H:i');

        return "Assalamu'alaikum Bapak/Ibu {$mustahik->nama}\n\n"
            ."Jadwal pengambilan beras ATM Beras Rogojampi telah dibuka.\n\n"
            ."Tanggal:\n{$tanggal}\n\n"
            ."Jam:\n{$jamMulai} - {$jamSelesai} WIB\n\n"
            ."Mesin:\n{$machine->machine_code}\n\n"
            ."Lokasi:\n{$machine->lokasi_penempatan}\n\n"
            ."Silakan datang sesuai jadwal untuk melakukan pengambilan beras.\n\n"
            .'Terima kasih.';
    }
}
