<?php

namespace App\Http\Controllers\Api;

use App\Models\Penjual;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class PenjualController extends Controller
{
    const PER_PAGE = 15;

    //menampilkan semua data penjual
    public function index(): JsonResponse
    {
        $penjual = Penjual::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return response()->json([
            'success' => true,
            'message' => 'Daftar penjual berhasil diambil',
            'data' => $penjual->items(),
            'pagination' => [
                'current_page' => $penjual->currentPage(),
                'last_page' => $penjual->lastPage(),
                'per_page' => $penjual->perPage(),
                'total' => $penjual->total(),
            ],
        ]);
    }

    //menambah data penjual
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|string|unique:penjual,email,',
            'nama' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        $penjual = Penjual::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Penjual berhasil ditambahkan',
            'data' => $penjual,
        ], 201);
    }

    //login penjual
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $penjual = Penjual::where('email', $request->email)->first();

        if (!$penjual || !Hash::check($request->password, $penjual->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $token = $penjual->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'role' => Penjual::ROLE,
            'data' => $penjual,
        ]);
    }

    //logout penjual
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
    
    //menampilkan data penjual sesuai id
    public function show($id): JsonResponse
    {
        $penjual = Penjual::find($id);

        if (!$penjual) {
            return response()->json([
                'success' => false,
                'message' => 'Penjual tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail penjual berhasil diambil',
            'data' => $penjual,
        ]);
    }

    //edit data penjual
    public function update(Request $request, $id): JsonResponse
    {
        $penjual = Penjual::find($id);

        if (!$penjual) {
            return response()->json([
                'success' => false,
                'message' => 'Penjual tidak ditemukan',
            ], 404);
        }

        $data = $request->validate([
            'email' => 'sometimes|required|string|unique:penjual,email,' . $id,
            'nama' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:6',    
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto_profile')) {
            if ($penjual->foto_profile) {
                Storage::disk('public')->delete($penjual->foto_profile);
            }
            $data['foto_profile'] = $request->file('foto_profile')
                ->store('profile_penjual', 'public');
        } elseif ($request->hapus_foto == '1') {
            Storage::disk('public')->delete($penjual->foto_profile);
            $data['foto_profile'] = null;
        }

        $penjual->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Penjual berhasil diperbarui',
            'data' => $penjual,
        ]);
    }

    //hapus data penjual
    public function delete($id): JsonResponse
    {
        $penjual = Penjual::find($id);
        if (!$penjual) {
            return response()->json([
                'success' => false,
                'message' => 'Penjual tidak ditemukan',
            ], 404);
        }

        if ($penjual->foto_profile) {
            Storage::disk('public')->delete($penjual->foto_profile);
        } 
    
        $penjual->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penjual berhasil dihapus',
        ]);
    }

    //lupa password penjual
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $penjual = Penjual::where('email', $request->email)->first();

        if (!$penjual) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan',
            ], 404);
        }

        $penjual->update([
            'password' => $request->password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset',
        ]);
    }
}