<?php

namespace App\Http\Controllers\Api;

use App\Models\Favorit;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoritRequest;
use App\Http\Requests\UpdateFavoritRequest;
use App\Http\Resources\FavoritResource;

class FavoritController extends Controller
{
    const PER_PAGE = 15;

    public function index(): JsonResponse
    {
        $favorit = Favorit::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return response()->json([
            'success' => true,
            'message' => 'Daftar favorit berhasil diambil',
            'data' => FavoritResource::collection($favorit),
            'pagination' => [
                'current_page' => $favorit->currentPage(),
                'last_page' => $favorit->lastPage(),
                'per_page' => $favorit->perPage(),
                'total' => $favorit->total(),
            ],
        ]);
    }

    public function store(StoreFavoritRequest $request): JsonResponse
    {
        $favorit = Favorit::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Favorit berhasil ditambahkan',
            'data' => new FavoritResource($favorit),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $favorit = Favorit::find($id);

        if (!$favorit) {
            return response()->json([
                'success' => false,
                'message' => 'Favorit tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail favorit berhasil diambil',
            'data' => new FavoritResource($favorit),
        ]);
    }

    public function update(UpdateFavoritRequest $request, $id): JsonResponse
    {
        $favorit = Favorit::find($id);

        if (!$favorit) {
            return response()->json([
                'success' => false,
                'message' => 'Favorit tidak ditemukan',
            ], 404);
        }

        $favorit->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Favorit berhasil diperbarui',
            'data' => new FavoritResource($favorit),
        ]);
    }

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
}
