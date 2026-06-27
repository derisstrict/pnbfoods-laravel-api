<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $detailOrderan = $this->detailOrderan ?? collect();
        $kantin = $detailOrderan->first()?->produk?->penjual?->kantin;
        return [
            'id' => $this->id,
            'status_orderan' => $this->status_orderan,
            'total_harga' => (float) $this->total_harga,
            'tanggal_orderan' => $this->tanggal_orderan,
            'pelanggan_id' => $this->pelanggan_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status_pembayaran' => $this->pembayaran?->status_pembayaran ?? 'Menunggu',
            'kantin' => $kantin ? [
                'nama_kantin'  => $kantin->nama_kantin,
                'kategori'     => $kantin->kategori,
                'foto_kantin'  => $kantin->foto_kantin,
            ] : null,

            'items' => $detailOrderan->map(fn($detail) => [
                'jumlah'         => $detail->jumlah,
                'nama_produk'    => $detail->produk?->nama_produk,
                'harga_subtotal' => $detail->produk?->harga_produk * $detail->jumlah,
            ])->values(),
        ];
    }
}
