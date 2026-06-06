<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Orderan extends Model
{
    protected $table = 'orderan';

    protected $fillable = [
        'pelanggan_id',
        'status_orderan',
        'total_harga',
        'tanggal_orderan',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'tanggal_orderan' => 'datetime',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function detailOrderan(): HasMany
    {
        return $this->hasMany(DetailOrderan::class);
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class);
    }
}
