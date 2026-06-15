<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'sometimes|string|max:255',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'deskripsi_produk' => 'nullable|string',
            'kategori_produk' => 'sometimes|string|max:100',
            'harga_produk' => 'sometimes|numeric|min:0',
            'stok' => 'sometimes|integer|min:-1',
            'penjual_id' => 'required|exists:penjual,id'
        ];
    }
}
