<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PembayaranResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'metode_pembayaran' => $this->metode_pembayaran,
            'total_pembayaran' => (float) $this->total_pembayaran,
            'status_pembayaran' => $this->status_pembayaran,
            'orderan_id' => $this->orderan_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
