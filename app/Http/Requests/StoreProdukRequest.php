<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'deskripsi_produk' => 'nullable|string',
            'kategori_produk' => 'required|string|max:100',
            'harga_produk' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ];
    }
}
