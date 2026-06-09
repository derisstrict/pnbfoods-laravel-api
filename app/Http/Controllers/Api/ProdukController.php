<?php

namespace App\Http\Controllers\Api;

use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Http\Resources\ProdukResource;

class ProdukController extends Controller
{
    const PER_PAGE = 15;

    public function index(): JsonResponse
    {
        $produk = Produk::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data' => ProdukResource::collection($produk),
            'pagination' => [
                'current_page' => $produk->currentPage(),
                'last_page' => $produk->lastPage(),
                'per_page' => $produk->perPage(),
                'total' => $produk->total(),
            ],
        ]);
    }

    public function dariPenjualId($penjualId): JsonResponse
    {
        $produk = Produk::where('penjual_id', $penjualId)
            ->orderBy('created_at', 'desc')
            ->paginate(self::PER_PAGE);

        if ($produk->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'data' => ProdukResource::collection($produk),
            'pagination' => [
                'current_page' => $produk->currentPage(),
                'last_page' => $produk->lastPage(),
                'per_page' => $produk->perPage(),
                'total' => $produk->total(),
            ],
        ]);
    }

    public function store(StoreProdukRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_produk')) {
            $data['foto_produk'] = $request->file('foto_produk')->store('produk', 'public');
        }

        $produk = Produk::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => new ProdukResource($produk),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil',
            'data' => new ProdukResource($produk),
        ]);
    }

    public function update(UpdateProdukRequest $request, $id): JsonResponse
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $data = $request->validated();

        if ($request->hasFile('foto_produk')) {
            if ($produk->foto_produk) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($produk->foto_produk);
            }
            $data['foto_produk'] = $request->file('foto_produk')->store('produk', 'public');
        }

        $produk->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => new ProdukResource($produk),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        if ($produk->foto_produk) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($produk->foto_produk);
        }

        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus',
        ]);
    }
}
