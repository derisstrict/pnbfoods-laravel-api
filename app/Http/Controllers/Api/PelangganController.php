<?php

namespace App\Http\Controllers\Api;

use App\Models\Pelanggan;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class PelangganController extends Controller
{
    const PER_PAGE = 15;

    //menampilkan semua data pelanggan
    public function index(): JsonResponse
    {
        $pelanggan = Pelanggan::orderBy('created_at', 'desc')->paginate(self::PER_PAGE);

        return response()->json([
            'success' => true,
            'message' => 'Daftar pelanggan berhasil diambil',
            'data' => $pelanggan->items(),
            'pagination' => [
                'current_page' => $pelanggan->currentPage(),
                'last_page' => $pelanggan->lastPage(),
                'per_page' => $pelanggan->perPage(),
                'total' => $pelanggan->total(),
            ],
        ]);
    }

    //menambah data pelanggan
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nim' => 'required|string|unique:pelanggan,nim',
            'nama' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);
        
        $pelanggan = Pelanggan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan',
            'data' => $pelanggan,
        ], 201);
    }

    //login pelanggan
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'nim' => 'required|string',
            'password' => 'required|string',
        ]);

        $pelanggan = Pelanggan::where('nim', $request->nim)->first();

        if (!$pelanggan || !Hash::check($request->password, $pelanggan->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIM atau password salah',
            ], 401);
        }

        $token = $pelanggan->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => $pelanggan,
        ]);
    }

    //logout pelanggan
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    //menampilkan data pelanggan sesuai id
    public function show($id): JsonResponse
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pelanggan berhasil diambil',
            'data' => $pelanggan,
        ]);
    }

    //edit data pelanggan
    public function update(Request $request, $id): JsonResponse
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        $data = $request->validate([
            'nim' => 'sometimes|required|string|unique:pelanggan,nim,' . $id,
            'nama' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:6',    
            'foto_profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('foto_profile')) {
            if ($pelanggan->foto_profile) {
                Storage::disk('public')->delete($pelanggan->foto_profile);
            }
            $data['foto_profile'] = $request->file('foto_profile')
                ->store('profil_pelanggan', 'public');
        } elseif ($request->hapus_foto == '1') {
            Storage::disk('public')->delete($pelanggan->foto_profile);
            $data['foto_profile'] = null;
        }

        $pelanggan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil diperbarui',
            'data' => $pelanggan,
        ]);
    }

    //hapus data pelanggan
    public function delete($id): JsonResponse
    {
        $pelanggan = Pelanggan::find($id);
        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        if ($pelanggan->foto_profile) {
            Storage::disk('public')->delete($pelanggan->foto_profile);
        } 

        $pelanggan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil dihapus',
        ]);
    }
}