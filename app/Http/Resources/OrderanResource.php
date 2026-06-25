<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status_orderan' => $this->status_orderan,
            'total_harga' => (float) $this->total_harga,
            'tanggal_orderan' => $this->tanggal_orderan,
            'pelanggan_id' => $this->pelanggan_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            //?db riwayat bagian detail item belanja
            'detail_orderan' => $this->whenLoaded('detailOrderan', function(){
                return $this->detailOrderan->map(function ($detail){
                    return[
                        'id' => $detail->id,
                        'jumlah' => $detail->jumlah,
                        'catatan' => $detail->catatan,
                        'produk' => $detail->produk ? [
                            'id' => $detail->produk->id,
                            'nama_produk' => $detail->produk->nama_produk,
                            'harga_produk' => (float) $detail->produk->harga_produk,
                            'foto_url' => $detail->produk->foto_url,
                        ] : null,
                        'subtotal' => $detail->produk
                            ? (float) $detail->produk->harga_produk * $detail->jumlah
                            : 0,
                    ];
                });
            }),

            //?db riwayat bagian info kantin
            'kantin' => $this->whenloaded('detailOrderan', function(){
                $kantin = $this->detailOrderan->first()?->produk?->penjual?->kantin;

                if(!$kantin){
                    return null;
                }
                return[
                    'id' => $kantin->id,
                    'nama_kantin' => $kantin->nama_kantin,
                    'kategori' => $kantin->kategori,
                    'foto_url' => $kantin->foto_url,
                ];
            }),

            //?db riwayat bagian info pembayaran
            'pembayaran' => $this->whenLoaded('pembayaran', function(){
                if(!$this->pembayaran){
                    return null;
                }
                return [
                    'id' => $this->pembayaran->id,
                    'metode_pembayaran' => $this->pembayaran->metode_pembayaran,
                    'total_pembayaran' => (float) $this->pembayaran->total_pembayaran,
                    'status_pembayaran' => $this->pembayaran->status_pembayaran,
                ];
            }),
        ];
    }
}
