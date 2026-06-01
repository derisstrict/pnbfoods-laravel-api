<?php

namespace App\Http\Controllers\Api;

use App\Models\Kantin;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KantinController extends Controller
{
    const PER_PAGE = 15;

    public function index(): JsonResponse
    {
        $kantin = Kantin::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kantin berhasil diambil',
            'data' => $kantin->items(),
            'pagination' => [
                'current_page' => $kantin->currentPage(),
                'last_page' => $kantin->lastPage(),
                'per_page' => $kantin->perPage(),
                'total' => $kantin->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_kantin' => 'required|string|max:255',
            'foto_kantin' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kategori' => 'required|string|max:100',
        ]);

        if ($request->hasFile('foto_kantin')) {
            $data['foto_kantin'] = $request->file('foto_kantin')->store('kantin', 'public');
        }

        $kantin = Kantin::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kantin berhasil ditambahkan',
            'data' => $kantin,
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $kantin = Kantin::find($id);

        if (!$kantin) {
            return response()->json([
                'success' => false,
                'message' => 'Kantin tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail kantin berhasil diambil',
            'data' => $kantin,
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $kantin = Kantin::find($id);

        if (!$kantin) {
            return response()->json([
                'success' => false,
                'message' => 'Kantin tidak ditemukan',
            ], 404);
        }

        $data = $request->validate([
            'nama_kantin' => 'sometimes|required|string|max:255',
            'foto_kantin' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'kategori' => 'sometimes|required|string|max:100',
        ]);

        if ($request->hasFile('foto_kantin')) {
            if ($kantin->foto_kantin) {
                Storage::disk('public')->delete($kantin->foto_kantin);
            }

            $data['foto_kantin'] = $request->file('foto_kantin')->store('kantin', 'public');
        }

        $kantin->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Kantin berhasil diperbarui',
            'data' => $kantin,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $kantin = Kantin::find($id);

        if (!$kantin) {
            return response()->json([
                'success' => false,
                'message' => 'Kantin tidak ditemukan',
            ], 404);
        }

        if ($kantin->foto_kantin) {
            Storage::disk('public')->delete($kantin->foto_kantin);
        }

        $kantin->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kantin berhasil dihapus',
        ]);
    }
}