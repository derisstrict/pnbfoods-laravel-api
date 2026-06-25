<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\FavoritResource;

class FavoritController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'pelanggan_id' => 'required|integer|exists:pelanggan,id',
        ]);

        $favorit = Favorit::with(['produk.penjual', 'produk.favorit'])
            ->where('pelanggan_id', $request->pelanggan_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar favorit berhasil diambil',
            'data'    => FavoritResource::collection($favorit),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'pelanggan_id' => 'required|integer|exists:pelanggan,id',
            'produk_id'    => 'required|integer|exists:produk,id',
        ]);

        // Cek apakah sudah difavoritkan
        $existing = Favorit::where('pelanggan_id', $request->pelanggan_id)
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($existing) {
            // Sudah ada → hapus (toggle off)
            $existing->delete();
            return response()->json([
                'success'    => true,
                'message'    => 'Produk dihapus dari favorit',
                'is_favorit' => false,
            ]);
        }

        // Belum ada → tambahkan
        $favorit = Favorit::create([
            'pelanggan_id' => $request->pelanggan_id,
            'produk_id'    => $request->produk_id,
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Produk berhasil ditambahkan ke favorit',
            'is_favorit' => true,
            'data'       => new FavoritResource($favorit->load(['produk.penjual', 'produk.favorit'])),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $favorit = Favorit::with(['produk.penjual', 'produk.favorit'])->find($id);

        if (!$favorit) {
            return response()->json([
                'success' => false,
                'message' => 'Favorit tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail favorit berhasil diambil',
            'data'    => new FavoritResource($favorit),
        ]);
    }

    /**
     * DELETE /api/favorit/{id}
     * Hapus favorit berdasarkan ID favorit
     */
    public function destroy($id): JsonResponse
    {
        $favorit = Favorit::find($id);

        if (!$favorit) {
            return response()->json([
                'success' => false,
                'message' => 'Favorit tidak ditemukan',
            ], 404);
        }

        $favorit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Favorit berhasil dihapus',
        ]);
    }

    /**
     * GET /api/favorit/cek?pelanggan_id=1&produk_id=2
     * Cek apakah produk tertentu sudah difavoritkan pelanggan
     */
    public function cek(Request $request): JsonResponse
    {
        $request->validate([
            'pelanggan_id' => 'required|integer|exists:pelanggan,id',
            'produk_id'    => 'required|integer|exists:produk,id',
        ]);

        $favorit = Favorit::where('pelanggan_id', $request->pelanggan_id)
            ->where('produk_id', $request->produk_id)
            ->first();

        return response()->json([
            'success'    => true,
            'is_favorit' => $favorit !== null,
            'favorit_id' => $favorit?->id,
        ]);
    }
}