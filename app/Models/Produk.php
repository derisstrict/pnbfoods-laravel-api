<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk';

    protected $fillable = [
        'penjual_id',
        'nama_produk',
        'foto_produk',
        'deskripsi_produk',
        'kategori_produk',
        'harga_produk',
        'stok',
    ];

    protected $casts = [
        'harga_produk' => 'decimal:2',
        'stok' => 'integer',
    ];

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto_produk) {
            return null;
        }
        return url('storage/' . $this->foto_produk);
    }

    public function penjual(): BelongsTo
    {
        return $this->belongsTo(Penjual::class);
    }

    public function detailOrderan(): HasMany
    {
        return $this->hasMany(DetailOrderan::class);
    }

    public function favorit(): HasMany
    {
        return $this->hasMany(Favorit::class);
    }
}
