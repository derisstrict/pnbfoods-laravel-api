<?php

namespace App\Http\Controllers\Api;

use App\Models\Orderan;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderanRequest;
use App\Http\Requests\UpdateOrderanRequest;
use App\Http\Resources\OrderanResource;

class OrderanController extends Controller
{
    const PER_PAGE = 15;

    public function index(): JsonResponse
    {
        $orderan = Orderan::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return response()->json([
            'success' => true,
            'message' => 'Daftar orderan berhasil diambil',
            'data' => OrderanResource::collection($orderan),
            'pagination' => [
                'current_page' => $orderan->currentPage(),
                'last_page' => $orderan->lastPage(),
                'per_page' => $orderan->perPage(),
                'total' => $orderan->total(),
            ],
        ]);
    }

    public function store(StoreOrderanRequest $request): JsonResponse
    {   
        $totalHarga = 0;
        foreach ($request->items as $item) {
            $produk = Produk::findOrFail($item['produk_id']);
            $totalHarga += $produk->harga_produk * $item['jumlah'];
        }

        $orderan = Orderan::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Orderan berhasil ditambahkan',
            'data' => new OrderanResource($orderan),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $orderan = Orderan::find($id);

        if (!$orderan) {
            return response()->json([
                'success' => false,
                'message' => 'Orderan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail orderan berhasil diambil',
            'data' => new OrderanResource($orderan),
        ]);
    }

    public function update(UpdateOrderanRequest $request, $id): JsonResponse
    {
        $orderan = Orderan::find($id);

        if (!$orderan) {
            return response()->json([
                'success' => false,
                'message' => 'Orderan tidak ditemukan',
            ], 404);
        }

        $orderan->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Orderan berhasil diperbarui',
            'data' => new OrderanResource($orderan),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $orderan = Orderan::find($id);

        if (!$orderan) {
            return response()->json([
                'success' => false,
                'message' => 'Orderan tidak ditemukan',
            ], 404);
        }

        $orderan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Orderan berhasil dihapus',
        ]);
    }

    public function getByPelanggan($pelangganId): JsonResponse
    {
        $orderan = Orderan::where('pelanggan_id', $pelangganId)
                          ->with(['detailOrderan.produk.penjual.kantin', 'pembayaran'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(self::PER_PAGE);
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar orderan pelanggan berhasil diambil',
            'data' => OrderanResource::collection($orderan),
            'pagination' => [
                'current_page' => $orderan->currentPage(),
                'last_page' => $orderan->lastPage(),
                'per_page' => $orderan->perPage(),
                'total' => $orderan->total(),
            ],
        ]);
    }
}
