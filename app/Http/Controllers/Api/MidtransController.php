<?php

namespace App\Http\Controllers\Api;

use App\Models\Orderan;
use App\Models\Pembayaran;
use App\Models\DetailOrderan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PembayaranResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans
    ) {}

    public function snapTransaction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'total_harga' => 'required|integer|min:1',
            'kantin_id' => 'required|exists:kantin,id',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.nama' => 'required|string|max:255',
            'items.*.harga' => 'required|integer|min:0',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string',
        ]);

        $existingPembayaran = Pembayaran::whereHas('orderan', function ($q) use ($validated) {
            $q->where('pelanggan_id', $validated['pelanggan_id'])
              ->where('status_orderan', 'menunggu_pembayaran');
        })->where('status_pembayaran', 'menunggu_pembayaran')
          ->where('expired_at', '>', now())
          ->where('created_at', '>', now()->subMinutes(30))
          ->latest()
          ->first();

        if ($existingPembayaran) {
            return response()->json([
                'success' => true,
                'message' => 'Menggunakan pembayaran yang sudah ada',
                'data' => [
                    'pembayaran' => new PembayaranResource($existingPembayaran),
                    'qr_image_url' => $existingPembayaran->qr_image_url,
                    'midtrans_order_id' => $existingPembayaran->midtrans_order_id,
                ],
            ], 200);
        }

        try {
            $result = DB::transaction(function () use ($validated) {
                $orderan = Orderan::create([
                    'pelanggan_id' => $validated['pelanggan_id'],
                    'status_orderan' => 'menunggu_pembayaran',
                    'total_harga' => $validated['total_harga'],
                    'tanggal_orderan' => now(),
                ]);

                foreach ($validated['items'] as $item) {
                    DetailOrderan::create([
                        'orderan_id' => $orderan->id,
                        'produk_id' => $item['produk_id'],
                        'jumlah' => $item['jumlah'],
                        'catatan' => $item['catatan'] ?? null,
                    ]);
                }

                $qris = $this->midtrans->createQrisTransaction($orderan);
                $expiredAt = $qris['expiry_time']
                    ? \Carbon\Carbon::parse($qris['expiry_time'], 'Asia/Jakarta')->utc()
                    : now()->addMinutes(15);

                $pembayaran = Pembayaran::create([
                    'orderan_id' => $orderan->id,
                    'metode_pembayaran' => 'QRIS',
                    'total_pembayaran' => $validated['total_harga'],
                    'status_pembayaran' => 'menunggu_pembayaran',
                    'midtrans_order_id' => $qris['midtrans_order_id'],
                    'qr_image_url' => $qris['qr_image_url'],
                    'midtrans_transaction_id' => $qris['midtrans_transaction_id'],
                    'expired_at' => $expiredAt,
                ]);

                return [
                    'pembayaran' => $pembayaran,
                    'qr_image_url' => $qris['qr_image_url'],
                    'midtrans_order_id' => $qris['midtrans_order_id'],
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pembayaran berhasil dibuat',
                'data' => [
                    'pembayaran' => new PembayaranResource($result['pembayaran']),
                    'qr_image_url' => $result['qr_image_url'],
                    'midtrans_order_id' => $result['midtrans_order_id'],
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Snap transaction failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function notification(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (!$this->midtrans->verifyNotification($payload)) {
            Log::warning('Midtrans notification signature mismatch', [
                'order_id' => $payload['order_id'] ?? 'unknown',
            ]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $midtransOrderId = $payload['order_id'];

        $pembayaran = Pembayaran::where('midtrans_order_id', $midtransOrderId)->first();

        if (!$pembayaran) {
            Log::warning('Pembayaran not found for notification', [
                'midtrans_order_id' => $midtransOrderId,
            ]);
            return response()->json(['message' => 'Pembayaran not found'], 404);
        }

        $currentStatus = $pembayaran->status_pembayaran;
        $terminalStates = ['lunas', 'refund', 'refund_sebagian'];

        if (in_array($currentStatus, $terminalStates)) {
            return response()->json(['message' => 'Already processed'], 200);
        }

        $newStatus = $this->midtrans->mapTransactionStatus(
            $payload['transaction_status'] ?? '',
            $payload['fraud_status'] ?? null
        );

        DB::transaction(function () use ($pembayaran, $newStatus, $payload) {
            $pembayaran->update([
                'status_pembayaran' => $newStatus,
                'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
                'midtrans_transaction_status' => $payload['transaction_status'] ?? null,
                'midtrans_response' => [
                    'payment_type' => $payload['payment_type'] ?? null,
                    'transaction_status' => $payload['transaction_status'] ?? null,
                    'fraud_status' => $payload['fraud_status'] ?? null,
                    'settlement_time' => $payload['settlement_time'] ?? null,
                    'transaction_time' => $payload['transaction_time'] ?? null,
                ],
            ]);

            if ($newStatus === 'lunas') {
                $pembayaran->orderan->update(['status_orderan' => 'diproses']);
            } elseif (in_array($newStatus, ['gagal', 'dibatalkan', 'kadaluwarsa'])) {
                $pembayaran->orderan->update(['status_orderan' => $newStatus]);
            }
        });

        return response()->json(['message' => 'OK'], 200);
    }

    public function status($id): JsonResponse
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan',
            ], 404);
        }

        if ($pembayaran->status_pembayaran === 'menunggu_pembayaran' && $pembayaran->midtrans_order_id) {
            try {
                try {
                    $midtransRaw = $this->midtrans->getTransactionStatus($pembayaran->midtrans_order_id);
                } catch (\Exception $e) {
                    $midtransRaw = $this->midtrans->getSnapTransactionStatus($pembayaran->midtrans_order_id);
                }

                $newStatus = $this->midtrans->mapTransactionStatus(
                    $midtransRaw['transaction_status'] ?? '',
                    $midtransRaw['fraud_status'] ?? null
                );

                if ($newStatus !== $pembayaran->status_pembayaran) {
                    DB::transaction(function () use ($pembayaran, $newStatus, $midtransRaw) {
                        $pembayaran->update([
                            'status_pembayaran' => $newStatus,
                            'midtrans_transaction_id' => $midtransRaw['transaction_id'] ?? null,
                            'midtrans_transaction_status' => $midtransRaw['transaction_status'] ?? null,
                            'midtrans_response' => [
                                'payment_type' => $midtransRaw['payment_type'] ?? null,
                                'transaction_status' => $midtransRaw['transaction_status'] ?? null,
                                'fraud_status' => $midtransRaw['fraud_status'] ?? null,
                                'settlement_time' => $midtransRaw['settlement_time'] ?? null,
                                'transaction_time' => $midtransRaw['transaction_time'] ?? null,
                            ],
                        ]);

                        if ($newStatus === 'lunas') {
                            $pembayaran->orderan->update(['status_orderan' => 'diproses']);
                        } elseif (in_array($newStatus, ['gagal', 'dibatalkan', 'kadaluwarsa'])) {
                            $pembayaran->orderan->update(['status_orderan' => $newStatus]);
                        }
                    });
                }
            } catch (\Exception $e) {
                Log::warning('Midtrans status poll failed', [
                    'pembayaran_id' => $id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => new PembayaranResource($pembayaran->fresh()),
        ]);
    }

    public function callback(Request $request): JsonResponse
    {
        $orderId = $request->query('order_id');

        $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PembayaranResource($pembayaran),
        ]);
    }
}
