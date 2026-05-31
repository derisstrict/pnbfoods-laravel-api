<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_produk' => $this->nama_produk,
            'foto_produk' => $this->foto_produk,
            'foto_url' => $this->foto_url,
            'deskripsi_produk' => $this->deskripsi_produk,
            'kategori_produk' => $this->kategori_produk,
            'harga_produk' => (float) $this->harga_produk,
            'stok' => $this->stok,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
