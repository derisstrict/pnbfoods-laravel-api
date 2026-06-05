<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orderan extends Model
{
    protected $table = 'orderan';

    protected $fillable = [
        'status_orderan',
        'total_harga',
        'tanggal_orderan',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'tanggal_orderan' => 'datetime',
    ];
}
