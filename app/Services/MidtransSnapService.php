<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransSnapService
{
    public function createTransaction(array $payload): array
    {
        $serverKey = (string) config('services.midtrans.server_key');
        $isProduction = (bool) config('services.midtrans.is_production', false);

        if ($serverKey === '') {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum diatur.');
        }

        $baseUrl = $isProduction
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->post($baseUrl . '/snap/v1/transactions', $payload);

        if (!$response->successful()) {
            throw new RuntimeException('Gagal membuat transaksi Midtrans: ' . $response->body());
        }

        return $response->json();
    }
}
