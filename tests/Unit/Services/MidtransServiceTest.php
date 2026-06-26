<?php

namespace Tests\Unit\Services;

use App\Services\MidtransService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MidtransServiceTest extends TestCase
{
    protected MidtransService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MidtransService();

        $reflection = new \ReflectionClass($this->service);
        $prop = $reflection->getProperty('serverKey');
        $prop->setAccessible(true);
        $prop->setValue($this->service, 'SB-Mid-test-key');
    }

    #[Test]
    public function it_verifies_notification_signature(): void
    {
        $orderId = 'ORDER-1-1234567890';
        $statusCode = '200';
        $grossAmount = '50000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . 'SB-Mid-test-key');

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
        ];

        $this->assertTrue($this->service->verifyNotification($payload));
    }

    #[Test]
    public function it_rejects_invalid_notification_signature(): void
    {
        $payload = [
            'order_id' => 'ORDER-1',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => 'invalid-signature',
        ];

        $this->assertFalse($this->service->verifyNotification($payload));
    }

    #[Test]
    public function it_maps_settlement_to_lunas(): void
    {
        $this->assertEquals('lunas', $this->service->mapTransactionStatus('settlement'));
    }

    #[Test]
    public function it_maps_capture_with_accept_to_lunas(): void
    {
        $this->assertEquals('lunas', $this->service->mapTransactionStatus('capture', 'accept'));
    }

    #[Test]
    public function it_maps_capture_with_deny_to_gagal(): void
    {
        $this->assertEquals('gagal', $this->service->mapTransactionStatus('capture', 'deny'));
    }

    #[Test]
    public function it_maps_pending_to_menunggu_pembayaran(): void
    {
        $this->assertEquals('menunggu_pembayaran', $this->service->mapTransactionStatus('pending'));
    }

    #[Test]
    public function it_maps_expire_to_kadaluwarsa(): void
    {
        $this->assertEquals('kadaluwarsa', $this->service->mapTransactionStatus('expire'));
    }

    #[Test]
    public function it_maps_deny_to_gagal(): void
    {
        $this->assertEquals('gagal', $this->service->mapTransactionStatus('deny'));
    }

    #[Test]
    public function it_maps_cancel_to_dibatalkan(): void
    {
        $this->assertEquals('dibatalkan', $this->service->mapTransactionStatus('cancel'));
    }

    #[Test]
    public function it_maps_unknown_status_to_menunggu_pembayaran(): void
    {
        $this->assertEquals('menunggu_pembayaran', $this->service->mapTransactionStatus('unknown_status'));
    }
}
