<?php

namespace App\Http\Controllers\Api;

use App\Models\DetailOrderan;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDetailOrderanRequest;
use App\Http\Requests\UpdateDetailOrderanRequest;
use App\Http\Resources\DetailOrderanResource;

class DetailOrderanController extends Controller
{
    const PER_PAGE = 15;

    public function index(): JsonResponse
    {
        $detailOrderan = DetailOrderan::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return response()->json([
            'success' => true,
            'message' => 'Daftar detail orderan berhasil diambil',
            'data' => DetailOrderanResource::collection($detailOrderan),
            'pagination' => [
                'current_page' => $detailOrderan->currentPage(),
                'last_page' => $detailOrderan->lastPage(),
                'per_page' => $detailOrderan->perPage(),
                'total' => $detailOrderan->total(),
            ],
        ]);
    }

    public function store(StoreDetailOrderanRequest $request): JsonResponse
    {
        $detailOrderan = DetailOrderan::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Detail orderan berhasil ditambahkan',
            'data' => new DetailOrderanResource($detailOrderan),
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $detailOrderan = DetailOrderan::find($id);

        if (!$detailOrderan) {
            return response()->json([
                'success' => false,
                'message' => 'Detail orderan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail orderan berhasil diambil',
            'data' => new DetailOrderanResource($detailOrderan),
        ]);
    }

    public function update(UpdateDetailOrderanRequest $request, $id): JsonResponse
    {
        $detailOrderan = DetailOrderan::find($id);

        if (!$detailOrderan) {
            return response()->json([
                'success' => false,
                'message' => 'Detail orderan tidak ditemukan',
            ], 404);
        }

        $detailOrderan->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Detail orderan berhasil diperbarui',
            'data' => new DetailOrderanResource($detailOrderan),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $detailOrderan = DetailOrderan::find($id);

        if (!$detailOrderan) {
            return response()->json([
                'success' => false,
                'message' => 'Detail orderan tidak ditemukan',
            ], 404);
        }

        $detailOrderan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Detail orderan berhasil dihapus',
        ]);
    }
}
