<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoritResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'pelanggan_id' => $this->pelanggan_id,
            'produk_id'    => $this->produk_id,
            'produk'       => [
                'id'              => $this->produk->id,
                'nama_produk'     => $this->produk->nama_produk,
                'foto_produk'     => $this->produk->foto_produk,
                'foto_url'        => $this->produk->foto_url,
                'deskripsi_produk'=> $this->produk->deskripsi_produk,
                'kategori_produk' => $this->produk->kategori_produk,
                'harga_produk'    => $this->produk->harga_produk,
                 'stok'            => $this->produk->stok,
                'kantin' => $this->produk->penjual && $this->produk->penjual->kantin ? [
                    'id'          => $this->produk->penjual->kantin->id,
                    'nama_kantin' => $this->produk->penjual->kantin->nama_kantin,
                    'foto_kantin' => $this->produk->penjual->kantin->foto_kantin,
                    'foto_url'    => $this->produk->penjual->kantin->foto_url,
                    'kategori'    => $this->produk->penjual->kantin->kategori,
                    'penjual_id'  => $this->produk->penjual->id,
                ] : null,
                'jumlah_favorit'  => $this->produk->favorit()->count(),
            ],
            'created_at'   => $this->created_at,
        ];
    }
}