<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailOrderan extends Model
{
    use HasFactory;
    protected $table = 'detail_orderan';

    protected $fillable = [
        'orderan_id',
        'produk_id',
        'jumlah',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function orderan(): BelongsTo
    {
        return $this->belongsTo(Orderan::class);
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
