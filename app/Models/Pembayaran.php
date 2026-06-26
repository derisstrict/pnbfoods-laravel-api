<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayaran';

    protected $fillable = [
        'orderan_id',
        'metode_pembayaran',
        'total_pembayaran',
        'status_pembayaran',
        'snap_token',
        'snap_redirect_url',
        'qr_image_url',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'midtrans_transaction_status',
        'midtrans_response',
        'expired_at',
    ];

    protected $casts = [
        'total_pembayaran' => 'decimal:2',
        'midtrans_response' => 'array',
        'expired_at' => 'datetime',
    ];

    public function orderan(): BelongsTo
    {
        return $this->belongsTo(Orderan::class);
    }
}
