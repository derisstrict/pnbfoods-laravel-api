<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'orderan_id',
        'metode_pembayaran',
        'total_pembayaran',
        'status_pembayaran',
    ];

    protected $casts = [
        'total_pembayaran' => 'decimal:2',
    ];

    public function orderan(): BelongsTo
    {
        return $this->belongsTo(Orderan::class);
    }
}
