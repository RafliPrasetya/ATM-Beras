<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $url;

    public function __construct()
    {
        $this->token = config('fonnte.token');
        $this->url = config('fonnte.url');
    }

    /**
     * Kirim satu pesan WhatsApp
     */
    public function sendMessage($target, $message)
    {
        try {

            $response = Http::timeout(config('fonnte.timeout'))
                ->retry(
                    config('fonnte.retry'),
                    config('fonnte.retry_delay')
                )
                ->withHeaders([
                    'Authorization' => $this->token,
                ])
                ->asForm()
                ->post($this->url, [
                    'target' => $target,
                    'message' => $message,
                ]);

            if ($response->successful()) {

                Log::info('WA BERHASIL', [
                    'target' => $target,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'response' => $response->json()
                ];
            }

            Log::error('WA GAGAL', [
                'target' => $target,
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'response' => $response->body()
            ];
        } catch (\Exception $e) {

            Log::error('WA ERROR', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'response' => $e->getMessage()
            ];
        }
    }

    private function formatPhoneNumber($number)
    {
        if (!$number) {
            return null;
        }

        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        if (!str_starts_with($number, '62')) {
            return null;
        }

        return $number;
    }
}
