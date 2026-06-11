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
        ];
    }
}
