<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'metode_pembayaran',
        'total_pembayaran',
        'status_pembayaran',
    ];

    protected $casts = [
        'total_pembayaran' => 'decimal:2',
    ];
}
