<?php

namespace App\Http\Controllers\Api;

use App\Models\Pembayaran;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePembayaranRequest;
use App\Http\Requests\UpdatePembayaranRequest;
use App\Http\Resources\PembayaranResource;

class PembayaranController extends Controller
{
    const PER_PAGE = 15;

    public function index(): JsonResponse
    {
        $pembayaran = Pembayaran::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return response()->json([
            'success' => true,
            'message' => 'Daftar pembayaran berhasil diambil',
            'data' => PembayaranResource::collection($pembayaran),
            'pagination' => [
                'current_page' => $pembayaran->currentPage(),
                'last_page' => $pembayaran->lastPage(),
                'per_page' => $pembayaran->perPage(),
                'total' => $pembayaran->total(),
            ],
        ]);
    }

    public function store(StorePembayaranRequest $request): JsonResponse
    {
        $pembayaran = Pembayaran::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil ditambahkan',
            'data' => new PembayaranResource($pembayaran),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pembayaran berhasil diambil',
            'data' => new PembayaranResource($pembayaran),
        ]);
    }

    public function update(UpdatePembayaranRequest $request, $id): JsonResponse
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan',
            ], 404);
        }

        $pembayaran->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diperbarui',
            'data' => new PembayaranResource($pembayaran),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran tidak ditemukan',
            ], 404);
        }

        $pembayaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dihapus',
        ]);
    }
}
