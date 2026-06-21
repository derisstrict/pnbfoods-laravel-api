<?php

namespace App\Services;

use App\Models\Orderan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $snapBaseUrl;
    protected string $coreBaseUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->snapBaseUrl = config('midtrans.snap_base_url');
        $this->coreBaseUrl = config('midtrans.core_base_url');
    }

    public function createQrisTransaction(Orderan $orderan): array
    {
        $orderId = 'ORDER-' . $orderan->id . '-' . now()->timestamp;

        $payload = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $orderan->total_harga,
            ],
            'customer_details' => [
                'first_name' => $orderan->pelanggan->nama ?? 'Customer',
            ],
        ];

        $response = Http::withBasicAuth($this->serverKey, '')
            ->acceptJson()
            ->post($this->coreBaseUrl . '/v2/charge', $payload);

        if (!$response->successful()) {
            Log::error('Midtrans QRIS creation failed', [
                'orderan_id' => $orderan->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Gagal membuat QRIS: ' . $response->body());
        }

        $body = $response->json();

        return [
            'midtrans_order_id' => $orderId,
            'qr_image_url' => $body['actions'][0]['url'] ?? null,
            'midtrans_transaction_id' => $body['transaction_id'] ?? null,
            'expiry_time' => $body['expiry_time'] ?? null,
        ];
    }

    public function verifyNotification(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $computed = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return hash_equals($computed, $signatureKey);
    }

    public function mapTransactionStatus(?string $transactionStatus, ?string $fraudStatus = null): string
    {
        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'accept' ? 'lunas' : 'gagal',
            'settlement' => 'lunas',
            'pending' => 'menunggu_pembayaran',
            'deny' => 'gagal',
            'cancel' => 'dibatalkan',
            'expire' => 'kadaluwarsa',
            'failure' => 'gagal',
            'refund' => 'refund',
            'partial_refund' => 'refund_sebagian',
            default => 'menunggu_pembayaran',
        };
    }

    public function getTransactionStatus(string $midtransOrderId): array
    {
        return $this->queryTransactionStatus($this->coreBaseUrl, $midtransOrderId);
    }

    public function getSnapTransactionStatus(string $midtransOrderId): array
    {
        return $this->queryTransactionStatus($this->snapBaseUrl, $midtransOrderId);
    }

    protected function queryTransactionStatus(string $baseUrl, string $midtransOrderId): array
    {
        $response = Http::withBasicAuth($this->serverKey, '')
            ->acceptJson()
            ->get($baseUrl . '/v2/' . $midtransOrderId . '/status');

        if (!$response->successful()) {
            $reason = $response->body();
            throw new \Exception(
                'Midtrans status check failed: HTTP ' . $response->status()
                . ' - ' . $reason
            );
        }

        $body = $response->json();

        if (!isset($body['transaction_status'])) {
            throw new \Exception(
                'Midtrans status check failed: ' . ($body['status_message'] ?? 'Unknown error')
            );
        }

        return $body;
    }
}
