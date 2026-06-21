<?php

namespace Tests\Feature\Api;

use App\Models\Orderan;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Penjual;
use App\Models\Kantin;
use App\Models\DetailOrderan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MidtransNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Pembayaran $pembayaran;
    protected Orderan $orderan;

    protected function setUp(): void
    {
        parent::setUp();

        $penjual = Penjual::factory()->create();
        $kantin = Kantin::factory()->create(['penjual_id' => $penjual->id]);
        $pelanggan = Pelanggan::factory()->create();
        $produk = Produk::factory()->create(['penjual_id' => $penjual->id]);

        $this->orderan = Orderan::factory()->create([
            'pelanggan_id' => $pelanggan->id,
            'status_orderan' => 'menunggu_pembayaran',
            'total_harga' => 50000,
            'tanggal_orderan' => now(),
        ]);

        DetailOrderan::factory()->create([
            'orderan_id' => $this->orderan->id,
            'produk_id' => $produk->id,
            'jumlah' => 2,
        ]);

        $this->pembayaran = Pembayaran::factory()->create([
            'orderan_id' => $this->orderan->id,
            'metode_pembayaran' => 'QRIS',
            'total_pembayaran' => 50000,
            'status_pembayaran' => 'menunggu_pembayaran',
            'midtrans_order_id' => 'ORDER-' . $this->orderan->id . '-1234567890',
        ]);
    }

    #[Test]
    public function it_updates_status_on_settlement_notification(): void
    {
        $serverKey = config('midtrans.server_key');
        $orderId = $this->pembayaran->midtrans_order_id;
        $signature = hash('sha512', $orderId . '200' . '50000.00' . $serverKey);

        $response = $this->postJson('/api/midtrans/notification', [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => $signature,
            'payment_type' => 'qris',
            'transaction_id' => 'trx-123',
            'fraud_status' => 'accept',
            'transaction_time' => now()->toDateTimeString(),
            'settlement_time' => now()->toDateTimeString(),
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('pembayaran', [
            'id' => $this->pembayaran->id,
            'status_pembayaran' => 'lunas',
            'midtrans_transaction_status' => 'settlement',
        ]);

        $this->assertDatabaseHas('orderan', [
            'id' => $this->orderan->id,
            'status_orderan' => 'diproses',
        ]);
    }

    #[Test]
    public function it_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/api/midtrans/notification', [
            'order_id' => $this->pembayaran->midtrans_order_id,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => 'invalid-signature',
            'payment_type' => 'qris',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('pembayaran', [
            'id' => $this->pembayaran->id,
            'status_pembayaran' => 'menunggu_pembayaran',
        ]);
    }

    #[Test]
    public function it_is_idempotent_for_duplicate_notifications(): void
    {
        $serverKey = config('midtrans.server_key');
        $orderId = $this->pembayaran->midtrans_order_id;
        $signature = hash('sha512', $orderId . '200' . '50000.00' . $serverKey);

        $payload = [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => $signature,
            'payment_type' => 'qris',
            'transaction_id' => 'trx-123',
            'fraud_status' => 'accept',
        ];

        $this->postJson('/api/midtrans/notification', $payload)->assertStatus(200);
        $this->postJson('/api/midtrans/notification', $payload)->assertStatus(200);

        $this->assertDatabaseHas('pembayaran', [
            'id' => $this->pembayaran->id,
            'status_pembayaran' => 'lunas',
        ]);
    }

    #[Test]
    public function it_does_not_regress_from_lunas(): void
    {
        $serverKey = config('midtrans.server_key');
        $orderId = $this->pembayaran->midtrans_order_id;
        $settlementSignature = hash('sha512', $orderId . '200' . '50000.00' . $serverKey);
        $expireSignature = hash('sha512', $orderId . '200' . '50000.00' . $serverKey);

        $this->postJson('/api/midtrans/notification', [
            'order_id' => $orderId,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => $settlementSignature,
            'payment_type' => 'qris',
            'fraud_status' => 'accept',
        ])->assertStatus(200);

        $this->postJson('/api/midtrans/notification', [
            'order_id' => $orderId,
            'transaction_status' => 'expire',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => $expireSignature,
            'payment_type' => 'qris',
        ])->assertStatus(200);

        $this->assertDatabaseHas('pembayaran', [
            'id' => $this->pembayaran->id,
            'status_pembayaran' => 'lunas',
        ]);
    }

    #[Test]
    public function it_returns_404_for_unknown_order(): void
    {
        $serverKey = config('midtrans.server_key');
        $signature = hash('sha512', 'unknown-order' . '200' . '50000.00' . $serverKey);

        $response = $this->postJson('/api/midtrans/notification', [
            'order_id' => 'unknown-order',
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => '50000.00',
            'signature_key' => $signature,
            'payment_type' => 'qris',
        ]);

        $response->assertStatus(404);
    }
}
