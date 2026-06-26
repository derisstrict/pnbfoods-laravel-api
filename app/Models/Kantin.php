<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kantin extends Model
{
    protected $table = 'kantin';

    protected $fillable = [
        'nama_kantin',
        'foto_kantin',
        'kategori',
        'penjual_id' //*aku nambah penjual id
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_kantin) {
            return null;
        }
        return url('storage/' . $this->foto_kantin);
    }

    // public function penjual(): HasOne
    // {
    //     return $this->hasOne(Penjual::class);
    // }

    public function penjual(): BelongsTo
    {
        return $this->belongsTo(Penjual::class);
    }
}
