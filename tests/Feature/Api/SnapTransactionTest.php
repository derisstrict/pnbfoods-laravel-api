<?php

namespace Tests\Feature\Api;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\Penjual;
use App\Models\Kantin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SnapTransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_order_and_payment_via_snap(): void
    {
        $penjual = Penjual::factory()->create();
        $kantin = Kantin::factory()->create(['penjual_id' => $penjual->id]);
        $pelanggan = Pelanggan::factory()->create();
        $produk = Produk::factory()->create([
            'penjual_id' => $penjual->id,
            'harga_produk' => 25000,
        ]);

        Http::fake([
            'api.sandbox.midtrans.com/*' => Http::response([
                'actions' => [
                    [
                        'name' => 'generate-qr-code',
                        'method' => 'GET',
                        'url' => 'https://api.sandbox.midtrans.com/v2/qris/xxx/qr-code',
                    ],
                ],
                'transaction_id' => 'mid-xxx',
            ], 201),
        ]);

        $response = $this->postJson('/api/pembayaran/snap', [
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => 50000,
            'kantin_id' => $kantin->id,
            'items' => [
                [
                    'produk_id' => $produk->id,
                    'nama' => $produk->nama_produk,
                    'harga' => 25000,
                    'jumlah' => 2,
                    'catatan' => 'Extra pedas',
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'pembayaran',
                    'qr_image_url',
                    'midtrans_order_id',
                ],
            ]);

        $this->assertDatabaseHas('orderan', [
            'pelanggan_id' => $pelanggan->id,
            'total_harga' => 50000,
            'status_orderan' => 'menunggu_pembayaran',
        ]);

        $this->assertDatabaseHas('pembayaran', [
            'metode_pembayaran' => 'QRIS',
            'status_pembayaran' => 'menunggu_pembayaran',
        ]);

        $this->assertDatabaseHas('detail_orderan', [
            'jumlah' => 2,
        ]);
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $response = $this->postJson('/api/pembayaran/snap', []);

        $response->assertStatus(422);
    }

    #[Test]
    public function it_polls_midtrans_api_when_status_is_still_pending(): void
    {
        $pelanggan = Pelanggan::factory()->create();

        $orderan = \App\Models\Orderan::factory()->create([
            'pelanggan_id' => $pelanggan->id,
            'status_orderan' => 'menunggu_pembayaran',
            'total_harga' => 30000,
        ]);

        $pembayaran = Pembayaran::factory()->create([
            'orderan_id' => $orderan->id,
            'status_pembayaran' => 'menunggu_pembayaran',
            'midtrans_order_id' => 'ORDER-' . $orderan->id . '-123456',
            'metode_pembayaran' => 'QRIS',
            'total_pembayaran' => 30000,
        ]);

        Http::fake([
            'api.sandbox.midtrans.com/*' => Http::response([
                'transaction_status' => 'settlement',
                'transaction_id' => 'mid-789',
                'payment_type' => 'qris',
                'settlement_time' => '2026-06-20 12:00:00',
                'transaction_time' => '2026-06-20 11:55:00',
                'fraud_status' => 'accept',
                'status_code' => '200',
                'gross_amount' => '30000',
                'order_id' => 'ORDER-' . $orderan->id . '-123456',
            ], 200),
        ]);

        $response = $this->getJson("/api/pembayaran/{$pembayaran->id}/status");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status_pembayaran' => 'lunas',
                ],
            ]);

        $this->assertDatabaseHas('pembayaran', [
            'id' => $pembayaran->id,
            'status_pembayaran' => 'lunas',
            'midtrans_transaction_id' => 'mid-789',
        ]);

        $this->assertDatabaseHas('orderan', [
            'id' => $orderan->id,
            'status_orderan' => 'lunas',
        ]);
    }
}
