<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrderan extends Model
{
    protected $table = 'detail_orderan';

    protected $fillable = [
        'jumlah',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];
}
